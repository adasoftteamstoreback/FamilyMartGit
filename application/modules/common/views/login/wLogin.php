<?php include(realpath(dirname(__FILE__) . '/../../../../config/config.php')); ?>
<!-- <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/globalcss/bootstrap/Ada.Grid.css"> -->
<link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/globalcss/bootstrapV3/bootstrap.min.css">

<script src="<?=$tBase_url?>application/modules/common/assets/js/global/jquery/jquery.js"></script>
<script src="<?=$tBase_url?>application/modules/common/assets/js/global/bootstrapV3/bootstrap.min.js"></script>


<style>
    html, body {
        height: 100%;
        background: linear-gradient(to right, #7f7fd5, #86a8e7, #91eae4); 
    }


    form{
        margin          : 0 auto 25px;
        margin-top      : 100px;
        width           : 100%;
        max-width       : 500px;
        background      : #FEFEFE;
        padding         : 30px;
        border          : 1px solid rgba(0,0,0,0.2);
        border-radius   : 5px;
        box-shadow      : 1px 2px 10px 0 rgba(0, 0, 0, 0.1);
    }

    #oetLogin{    
        width       : 100%;
        margin-top  : 15px;
    }

    #oahForgot{
        float       : right; 
        font-size   : 13px; 
        font-style  : inherit;
    }

    #oetPassword , #oetEmail{
        width       :100%; 
        height      :35px;
    }
</style>

<div class="containner-fluid odvContentlogin">
    <div class="row-fluid">
        <div class="col-12">
            <form>
                <img src="<?=$tBase_url?>application/modules/common/assets/images/logo/logo.png" style="width: 50%; margin: 10px auto; display: block; cursor: pointer;" >
                
                <div><hr></div>

                <div class="form-group">
                    <label>Email</label>
                    <div class="input-group col-12">
                        <input type="text" class="form-control" name="oetEmail" id="oetEmail" placeholder="Enter email">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group col-12">
                        <input type="password" class="form-control" name="oetPassword" id="oetPassword" placeholder="******">
                    </div>
                </div>

                <div>
                    <button id="oetLogin" type="button" class="btn btn-primary" onclick="JSxCheckLogin();">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        
    });

    function JSxCheckLogin(){
        $.ajax({
            url     : 'application/modules/common/controllers/cChecklogin.php',
            data    : { tParamter : '-' },
            type    : 'POST',
            success : function(result){
                window.location.href = 'index.php';
            }
        });
    }
</script>