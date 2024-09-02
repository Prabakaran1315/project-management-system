function successToast(message){
    $.toast({
        heading: 'Success',
        text: message ?? 'Success',
        showHideTransition: 'slide',
        icon: 'success',
        position: 'bottom-right'
    });
}

function warningToast(message){
    $.toast({
        heading: 'Warning',
        text: message ?? 'Failed',
        showHideTransition: 'slide',
        icon: 'warning',
        position: 'bottom-right'
    });
}