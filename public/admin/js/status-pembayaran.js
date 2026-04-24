document.addEventListener('DOMContentLoaded', function () {
    const approveButtons = document.querySelectorAll('.approve-btn');
    const rejectButtons = document.querySelectorAll('.reject-btn');

    approveButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const form = this.closest('form');
            const url = form.action;
            const formData = new FormData(form);

            axios.post(url, formData)
                .then(response => {
                    if (response.data.success) {
                        const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                        modal.hide();
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        });
    });

    rejectButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const form = this.closest('form');
            const url = form.action;
            const formData = new FormData(form);

            axios.post(url, formData)
                .then(response => {
                    if (response.data.success) {
                        const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                        modal.hide();
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        });
    });
});
