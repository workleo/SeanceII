function checkName() {
    var name = $('#name').val();

    if(name.length != 0 ) {
        $('#btn_submit').removeAttr('disabled');
    } else {
        $('#btn_submit').attr('disabled', 'disabled');
    }
}
