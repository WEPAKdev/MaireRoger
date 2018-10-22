/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function(){
    $(document).on('click', '#validCommand', function(){
        validCommand();
        $(this).attr('disabled','disabled');
    });
    
    $(document).on('click', '#bt-pdf', function(){
        $('#validCommand').removeAttr('disabled');
    })

    function validCommand() {
        var baseDir = "/";
        $.ajax({
            type: 'GET',
            url: baseDir + 'modules/stockfournisseur/ajax/actions.php',
            headers: { "cache-control": "no-cache" },
            async: true,
            cache: false,
            data: 'action=validCommandFourn',
            success: function(html){
                console.log(html);
                $('#toorder-container').remove();
                $(html).insertAfter($('.kpi-container'));
            }
        });
    }
})

