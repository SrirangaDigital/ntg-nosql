<div class="container">
    <div class="row">
        <div class="col-md-1">&nbsp;</div>
        <div class="col-md-10">
            <div class="spp-general">
                <p class="title">registration</p>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-md-1">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-5">
            <form method="post" action="<?=BASE_URL . 'user/processRegistration'?>">
                <div class="form-group text-right">
                    <p class="help-block red"><?=$data['errMsg']?></p>
                </div>
                <div class="form-group">
                    <label for="name">Name*</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?=$data['name']?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email address*</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?=$data['email']?>" required>
                </div>
                <div class="form-group">
                    <label for="lemail">Profession*</label>
                    <input type="text" class="form-control" id="profession" name="profession" value="<?=$data['profession']?>" required>
                </div>
                <div class="form-group">
                    <label for="affiliation">Affiliation</label>
                    <textarea class="form-control" name="affiliation" rows="4" placeholder="Please tell us about your affiliation."><?=$data['affiliation']?></textarea>
                </div>
                <div class="form-group">
                    <label for="password">Password*</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="cpassword">Confirm password*</label>
                    <input type="password" class="form-control" id="cpassword" name="cpassword" required>
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
