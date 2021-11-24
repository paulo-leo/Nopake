/*!
    * Start Bootstrap - np Admin v7.0.3 (https://startbootstrap.com/template/np-admin)
    * Copyright 2013-2021 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-np-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('np|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('np-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('np-sidenav-toggled');
            localStorage.setItem('np|sidebar-toggle', document.body.classList.contains('np-sidenav-toggled'));
        });
    }

});
