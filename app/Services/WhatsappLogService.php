<?php

namespace App\Services;

use App\Enums\JenisWhatsapp;
use App\Models\Reservasi;
use App\Models\WhatsappLog;
use App\Support\WhatsappLink;
use InvalidArgumentException;

class WhatsappLogService
{
    public function reservationCreated(Reservasi $reservasi): WhatsappLog
    {
        $reservasi->loadMissing(['pelanggan', 'kendaraan.merk', 'slot', 'layanan']);

        return $this->create(
            $reservasi,
            JenisWhatsapp::ReservasiBaru,
            $this->reservationMessage(
                $reservasi,
                "Halo {$this->customerName($reservasi)}, reservasi {$reservasi->kode} untuk {$this->vehicleLabel($reservasi)} pada {$this->scheduleLabel($reservasi)} sudah kami terima dan sedang menunggu konfirmasi admin."
            )
        );
    }

    public function reservationConfirmed(Reservasi $reservasi): WhatsappLog
    {
        return $this->create(
            $reservasi,
            JenisWhatsapp::KonfirmasiReservasi,
            $this->messageFor($reservasi, JenisWhatsapp::KonfirmasiReservasi)
        );
    }

    public function reservationCancelled(Reservasi $reservasi, ?string $reason = null): WhatsappLog
    {
        return $this->create(
            $reservasi,
            JenisWhatsapp::PembatalanReservasi,
            $this->messageFor($reservasi, JenisWhatsapp::PembatalanReservasi, $reason)
        );
    }

    public function reservationRejected(Reservasi $reservasi, string $reason): WhatsappLog
    {
        return $this->create(
            $reservasi,
            JenisWhatsapp::PenolakanReservasi,
            $this->messageFor($reservasi, JenisWhatsapp::PenolakanReservasi, $reason)
        );
    }

    public function serviceCompleted(Reservasi $reservasi): WhatsappLog
    {
        return $this->create(
            $reservasi,
            JenisWhatsapp::ServisSelesai,
            $this->messageFor($reservasi, JenisWhatsapp::ServisSelesai)
        );
    }

    public function messageFor(Reservasi $reservasi, JenisWhatsapp $jenis, ?string $reason = null): string
    {
        $reservasi->loadMissing(['pelanggan', 'kendaraan.merk', 'slot', 'layanan', 'servis']);

        return match ($jenis) {
            JenisWhatsapp::KonfirmasiReservasi => $this->confirmationMessage($reservasi),
            JenisWhatsapp::PenolakanReservasi => $this->rejectionMessage($reservasi, $reason),
            JenisWhatsapp::PembatalanReservasi => $this->cancellationMessage($reservasi, $reason),
            JenisWhatsapp::ServisSelesai => $this->completedMessage($reservasi),
            JenisWhatsapp::PengingatServis => $this->reminderMessage($reservasi),
            default => throw new InvalidArgumentException("Jenis pesan {$jenis->value} tidak dapat dikirim manual."),
        };
    }

    public function logManualSend(Reservasi $reservasi, JenisWhatsapp $jenis, string $message): WhatsappLog
    {
        return $this->create($reservasi, $jenis, $message);
    }

    private function confirmationMessage(Reservasi $reservasi): string
    {
        $message = "Halo {$this->customerName($reservasi)}, booking {$reservasi->kode} disetujui. Jadwal: {$this->scheduleLabel($reservasi)}.";
        $layanan = $this->serviceLabel($reservasi);

        if ($layanan !== '') {
            $message .= " Layanan: {$layanan}.";
        }

        return $message." Motor: {$this->vehicleLabel($reservasi)}. Silakan datang sesuai jadwal.";
    }

    private function rejectionMessage(Reservasi $reservasi, ?string $reason = null): string
    {
        $reason = trim((string) ($reason ?? $this->rejectionReason($reservasi)));
        $reason = $reason !== '' ? $reason : 'Jadwal yang dipilih tidak tersedia';

        return "Halo {$this->customerName($reservasi)}, mohon maaf booking {$reservasi->kode} untuk {$this->vehicleLabel($reservasi)} pada {$this->scheduleLabel($reservasi)} ditolak. Alasan: {$reason}. Silakan pilih jadwal lain melalui halaman booking. Terima kasih.";
    }

    private function cancellationMessage(Reservasi $reservasi, ?string $reason = null): string
    {
        $message = "Halo {$this->customerName($reservasi)}, reservasi {$reservasi->kode} untuk {$this->vehicleLabel($reservasi)} pada {$this->scheduleLabel($reservasi)} telah dibatalkan.";

        if ($reason) {
            $message .= " Alasan: {$reason}.";
        }

        return $message;
    }

    private function completedMessage(Reservasi $reservasi): string
    {
        return "Halo {$this->customerName($reservasi)}, motor {$this->vehicleLabel($reservasi)} sudah selesai diservis. Silakan diambil di bengkel. Reservasi: {$reservasi->kode}. Terima kasih.";
    }

    private function reminderMessage(Reservasi $reservasi): string
    {
        $layanan = $this->serviceLabel($reservasi);
        $layananLabel = $layanan !== '' ? "servis berkala {$layanan}" : 'servis berkala';

        return "Halo {$this->customerName($reservasi)}, sudah waktunya {$layananLabel} untuk motor {$this->vehicleLabel($reservasi)}. Silakan buat jadwal servis melalui halaman booking. Terima kasih.";
    }

    private function rejectionReason(Reservasi $reservasi): string
    {
        if (preg_match('/(?:Ditolak|Dibatalkan) admin:\s*(.+)/', (string) $reservasi->catatan, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }

    private function create(Reservasi $reservasi, JenisWhatsapp $jenis, string $message): WhatsappLog
    {
        $reservasi->loadMissing('pelanggan');

        $phone = $reservasi->pelanggan?->no_hp;
        $normalized = WhatsappLink::normalize($phone);

        return WhatsappLog::create([
            'user_id' => $reservasi->user_id,
            'reservasi_id' => $reservasi->id,
            'jenis' => $jenis,
            'nomor_tujuan' => $normalized ?? (trim((string) $phone) !== '' ? trim((string) $phone) : '-'),
            'pesan' => $message,
            'status' => 'menunggu',
        ]);
    }

    private function reservationMessage(Reservasi $reservasi, string $message): string
    {
        $layanan = $this->serviceLabel($reservasi);

        return $layanan === '' ? $message : "{$message} Layanan: {$layanan}.";
    }

    private function customerName(Reservasi $reservasi): string
    {
        return $reservasi->pelanggan?->name ?? 'Pelanggan';
    }

    private function serviceLabel(Reservasi $reservasi): string
    {
        return $reservasi->layanan->pluck('nama')->join(', ');
    }

    private function vehicleLabel(Reservasi $reservasi): string
    {
        $plat = $reservasi->kendaraan?->plat_nomor ?? '-';
        $merk = $reservasi->kendaraan?->merk?->nama;

        return $merk ? "{$plat} ({$merk})" : $plat;
    }

    private function scheduleLabel(Reservasi $reservasi): string
    {
        $slot = $reservasi->slot;

        if (! $slot) {
            return '-';
        }

        return $slot->tanggal->translatedFormat('d M Y').' pukul '.substr($slot->jam_mulai, 0, 5).'–'.substr($slot->jam_selesai, 0, 5);
    }
}
