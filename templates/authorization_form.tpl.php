<div style="width: 200px; margin: 0 auto;">
    <form class="claroLoginForm" method="post">
        <fieldset>
            <legend><?php echo get_lang('Authorization Requested'); ?></legend>
            <label><?php echo get_lang('Do you authorize __NAME__ ?', array('__NAME__' => $this->clientName)); ?></label><br /><br />
            <input type="submit" name="authorized" value="<?php echo get_lang('Yes'); ?>" style="width:48%">
            <input type="submit" name="refused" value="<?php echo get_lang('No'); ?>"  style="width:48%">
        </fieldset>
    </form>
</div>
