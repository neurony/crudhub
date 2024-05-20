import Swal from 'sweetalert2';

class Flash {
    /**
     * Create a new Errors instance.
     */
    constructor() {
        this.flash = Swal.mixin({
            toast: true,
            position: 'top-end',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast'
            },
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    }

    /**
     * Fire Swal toast
     *
     * @param message
     * @param icon
     */
    fire(message = null, icon = null) {
        this.flash.fire({
            icon: icon,
            title: message
        });
    }

    /**
     * Fire Swal success toast
     *
     * @param message
     */
    success(message = '') {
        this.fire(message, 'success');
    }

    /**
     * Fire Swal error toast
     *
     * @param message
     */
    error(message = '') {
        this.fire(message, 'error');
    }

    /**
     * Fire Swal warning toast
     *
     * @param message
     */
    warning(message = '') {
        this.fire(message, 'warning');
    }

    /**
     * Fire Swal info toast
     *
     * @param message
     */
    info(message = '') {
        this.fire(message, 'info');
    }
}

export default new Flash;
