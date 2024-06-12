const deleted = (component, method, elementId) => {
    Swal.fire({
        title: "Are you sure?",
        text: "¡You can't reverse this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete!",
    }).then((result) => {
        if (result.isConfirmed) {
            livewire.emitTo(component,method, elementId)
            Swal.fire(
                "Removed!",
                "Your registration has been deleted",
                "success"
            );
        }
    });
};

const  alertSuccess  = (message) => {
    Swal.fire({
        title: "Éxito!",
        text: message,
        icon: "success"
      });
};


const alertFailed = (message) => {
    Swal.fire({
        icon: 'error',
        title: message,
        showConfirmButton: false,
        timer: 1500
     })
} 