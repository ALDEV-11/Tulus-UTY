                </main>
                @include('admin.partials.footer')
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<script src="{{ asset('dist-dashboard/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('dist-dashboard/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('dist-dashboard/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('dist-dashboard/assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('dist-dashboard/assets/js/main.js') }}"></script>
<script src="{{ asset('dist-dashboard/assets/js/live-search.js') }}"></script>
@include('admin.partials.realtime')
<p class="visually-hidden" role="status" aria-live="polite" data-live-search-status></p>
@include('partials.sweetalert')
@stack('scripts')
</body>
</html>
