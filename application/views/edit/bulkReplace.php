<div class="container">
    <div class="row">
        <div class="col-md-1">&nbsp;</div>
        <div class="col-md-10">
            <div class="spp-general">
                <p class="title">Bulk Replace</p>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-md-1">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-5">
            <form id="bulkreplace" method="post" action="<?=BASE_URL . 'data/bulkReplaceAction'?>">
                <div class="form-group">
                    <label for="key">Key</label>
                    <input type="text" class="form-control" id="key" name="key" placeholder="Key" required>
                </div>
                <div class="form-group">
                    <label for="oldValue">Old Value</label>
                    <input type="text" class="form-control" id="oldValue" name="oldValue" placeholder="Old Value" required>
                </div>
                <div class="form-group">
                    <label for="newValue">New Value</label>
                    <input type="text" class="form-control" id="newValue" name="newValue" placeholder="New Value" required>
                </div>
                <button type="submit" class="btn btn-default" value="submit" name="submit">Submit</button>
            </form>
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
</div>
