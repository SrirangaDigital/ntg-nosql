<div class="container">
    <div class="row">
        <div class="col-md-1">&nbsp;</div>
        <div class="col-md-10">
            <div class="spp-general">
                <p class="title">reset</p>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-md-1">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-5">
            <form method="post" action="<?=BASE_URL . 'user/insertNewPassword'?>">
                <div class="form-group text-right">
                    <p class="help-block red"><?=$data['errMsg']?></p>
                </div>
                <div class="form-group">
                    <label for="password">New password*</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="cpassword">Confirm new password*</label>
                    <input type="password" class="form-control" id="cpassword" name="cpassword" required>
                    <input type="hidden" name="hash" value="<?=$data['hash']?>" required>
                </div>
                <div class="form-group gap-above-small">
                    <?php $viewHelper->insertReCaptcha(); ?>
                </div>
                <button type="submit" class="btn btn-default gap-above-small" value="submit" name="submit">Submit</button>
            </form>
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
</div>
