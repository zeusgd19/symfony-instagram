$(document).ready(
    function (){
        inputs = $('input');
        submit = $('#login-button');

        $(inputs).on('input',function (){

            if(inputs[0].value && inputs[1].value){
                $(submit).attr('disabled',false);
            }else {
                $(submit).attr('disabled',true);
            }
        })
    }
)