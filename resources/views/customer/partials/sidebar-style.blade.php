<style>
    #layout-menu .menu-inner {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    #layout-menu .menu-inner::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }

    #layout-menu .ps__rail-x,
    #layout-menu .ps__rail-y,
    #layout-menu .ps__thumb-x,
    #layout-menu .ps__thumb-y {
        display: none !important;
    }

    .navbar-user-avatar {
        width: 2.5rem;
        height: 2.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: var(--bs-primary);
        color: #fff;
    }

    .navbar-user-avatar .avatar-initial {
        width: 100%;
        height: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: transparent !important;
        color: #fff !important;
        font-weight: 600;
        line-height: 1;
    }
</style>
