  function ValueEnter(){    
        var CutoffCheck;
        var Values=[];
        var Amounts=[];
       
        for(i=1;i<=Attemption;i++) {
           disciptions[i]=""; 
        }
        
        $("#limit").css('color', 'black'); 
        
        for(i=1;i<=Attemption;i++) {
            $('#value' + i).css('color', 'black'); 
            Values[i] = $('#value' + i).val();
            Amounts[i] = $('#amount' + i).val();
            if(!limits[i]){
                $('#value' + i).css('color', 'red');     
                $('#limit').css('color', 'red');                 
                disciptions[i]='Not passed Limit';
            }
        }
        
        if(isCutoff){
             $("#cutoff").css('color', 'black'); 
             for(i=1;i<=Attemption;i++) {        
                 if(limits[i]){
                    $('#value' + i).css('color', 'black');     
                 }
             }
             CutoffCheck=false;
             for(i=1;i<CutoffN;i++) {   
                 CutoffCheck = cutoffs[i] || CutoffCheck;
             }
             if(!CutoffCheck){
                 for(i=CutoffN;i<=Attemption;i++) {        
                     $('#value' + i).css('color', 'red');
                     //document.getElementById('value'+ i).style.color='red';        
                     if(Values[i]!==""){
                        disciptions[i]='Not passed Cutoff';
                     }
                 }
                $('#cutoff').css('color', 'red');
                $('#cutoff_hr').css('background', 'red');
             }else{
                $('#cutoff').css('color', 'green');
                $('#cutoff_hr').css('background', 'green');
             }                                 

        }
        for(i=1;i<=Attemption;i++){
            if((!isCutoff || CutoffCheck || i<CutoffN) && Values[i]===''){
                disciptions[i]='No result';
            }
            if(document.getElementById('amount'+ i)!==null){
                if(Values[i]==='' && Amounts[i]!==''){
                    disciptions[i]='No time';    
                }
                if(Values[i]!=='' && Amounts[i]===''){
                    disciptions[i]='No amount';    
                }
                if(Values[i]!=='DNS' && Values[i]!=='DNF' && Amounts[i]==='0'){
                    disciptions[i]='No amount';    
                }
                
                
            }
        }
        var AttempsWarning = '';
        
        submitResult="";
        for(i=1;i<=Attemption;i++){
            document.getElementById('description'+i).innerHTML=disciptions[i];
            if(disciptions[i]!==''){
                submitResult= submitResult + i+ ': ' +  disciptions[i] +'\n';
                AttempsWarning = AttempsWarning + i + ','; 
            }
        }
        
        $('#AttempsWarning').val(AttempsWarning);
    }
     
function ValueEnterOne(i){
        var value = $('#value' + i).val();

        $( "#SubmitValue" ).prop( "disabled", false );
        var next_value='value'+(i+1);
        var next_amount='amount'+(i+1);
        value=value.replace('(','');
        value=value.replace(')','');
        if((value.length===1 & 'вВаАdDfF*'.indexOf(value)!==-1) | value==='DNF' ){
            value='DNF';
            $('#amount'+i).val(0);
            AmountEnterOne(i);
            if(i<Attemption){
                if(document.getElementById(next_value)!==null){
                   document.getElementById(next_value).focus();
                }
                if(document.getElementById(next_amount)!==null){
                    document.getElementById(next_amount).focus();
                }
            }
        }else if((value.length===1 & 'ыЫsS/'.indexOf(value)!==-1) | value==='DNS'){
            value='DNS';
            $('#amount'+i).val(0);
            AmountEnterOne(i);
            if(i<Attemption){
                if(document.getElementById(next_value)!==null){
                   document.getElementById(next_value).focus();
                }
                if(document.getElementById(next_amount)!==null){
                    document.getElementById(next_amount).focus();
                }
            }
        }else{
            value=value.replace(/\D+/g,'');
            value=value.replace(/^0+/,'');   
            value=value.substring(0,7);

            var minute=0;
            var second=0;
            var milisecond=0;

            if(value.length===1){
                value='0.0' + value ;
            }else if(value.length===2){
                value='0.' + value ; 
            }else if(value.length===3){
                second=Number.parseInt(value.substr(0,1));
                value=value.substr(0,1) + '.' + value.substr(1,2) ;
            }else if(value.length===4){
                second=Number.parseInt(value.substr(0,2));
                value=value.substr(0,2) + '.' + value.substr(2,2) ;
            }else if(value.length===5){
                second=Number.parseInt(value.substr(1,2));
                minute=Number.parseInt(value.substr(0,1));
                value=value.substr(0,1) + ':' + value.substr(1,2) + '.' + value.substr(3,2) ;
            }else if(value.length===6){
                second=Number.parseInt(value.substr(2,2));
                minute=Number.parseInt(value.substr(0,2));
                milisecond=Number.parseInt(value.substr(4,2));
                if(milisecond>=50){
                    second=second+1;
                }
                if(second===60){
                    second=0;
                    minute=minute+1;
                }
                value=('0'+minute).substr(-2,2)  + ':' + ('0'+second).substr(-2,2) + '.00' ;
            }else{
                value='';
            }                    
       }

        if(isCutoff){
            cutoffs[i] = (value!=='' && value!=='DNF' && value!=='DNS' && (second + minute * 60) < (cutoff_second + cutoff_minute * 60) );
        }
        limits[i] = (value==='' || value==='DNF' || value==='DNS' || (second + minute * 60) < (limit_second + limit_minute * 60) );                   

        $('#value' + i).val(value);
        if(value==='DNF' | value==='DNS'){
            $('#value'+i).css('background', 'yellow');
        }else{
            $('#value'+i).css('background', 'white');
        }
    ValueEnter();
}
        
function AmountEnterOne(i){
        if(document.getElementById('amount'+i)===null){return;}
            
            var amount = $('#amount' + i).val();  
            $( "#SubmitValue" ).prop( "disabled", false );
            amount=amount.replace(/\D+/g,'');
            $('#amount' + i).val(amount);

            if(amount==='0'){
                $('#amount'+i).css('background', 'yellow');
            }else{
                $('#amount'+i).css('background', 'white');
            }
            ValueEnter();
        }

 
 function  chosenSelectCommandID(n){
    $('#Registration option').each(function (){$(this).removeAttr("selected");});
    $("#CommandIDSelect" + n).attr("selected","selected");
    $(".chosen-select").trigger("chosen:updated.chosen");
    PrepareInputs(false);

    if(n>0){
        $('#Type').val('Command');
        
        for(var i=1;i<=Attemption;i++) {
        
            $('#amount'+i).val(AmountsSave[n+'_'+i]);
            $('#value'+i).val(ValuesSave[n+'_'+i]);
           
           
            ValueEnterOne(i);
            AmountEnterOne(i);
        }
    }else{
        $('#Type').val('');
    }
}   


function  chosenSelectCompetitorID(){
    $('#Type').val('Competitors');
    for(var i=1;i<=Attemption;i++) {
       $('.description'+i).html(''); 
    }

    $( "#SubmitValue" ).prop( "disabled", false );
    
    PrepareInputs(true);

    for(var i=1;i<=Attemption;i++) {
        ValueEnterOne(i);
        AmountEnterOne(i);        
    }
}
    

 function ClickRow(n){
    $('#Type').val('Command');
    $(".chosen-select").val($(".search-field").val());
    
    SelectUpdate();
    $('.CommandSelect').each(function (){$(this).removeAttr("disabled");});
    
    chosenSelectCommandID(n);
    
    SetSelectedOption(1);  
    $('#value1').focus();
    $('#amount1').focus();
}

function SetSelectedOption(n){
    $('.chosen-select').chosen('destroy').chosen({ max_selected_options:n });  
}

function SelectUpdate(){
    $('.chosen-select').trigger('chosen:updated.chosen');
}


function PrepareInputs(disabled){
    $('.amount_input, .value_input')
            .css('background', 'white')
            .prop('disabled', disabled )
            .val('');    
    $( "#SubmitValue" ).prop( "disabled", !disabled );
    $('.description').html('');
}