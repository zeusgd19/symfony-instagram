$(document).ready(function (){
    inputs = $('input');
    submit = $('#register-button');

    $(inputs).on('input',function (){
        if(inputs[0].value && inputs[1].value &&inputs[2].value){
            $(submit).attr('disabled',false);
        }else{
            $(submit).attr('disabled',true);
        }
    })
})