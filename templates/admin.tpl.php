<form class="claroLoginForm" method="post" style="width: 100%; margin: auto;">
    <fieldset class="collapsible">
        <legend><a href="#" class="doCollapse"><?php echo get_lang( 'Registered Clients' ); ?></a></legend>
        <div class="collapsible-wrapper">
            <table class="claroTable emphaseLine">
                <thead>
                <tr class="headerX">
                    <th align="center" style="width: 20%;"><?php echo get_lang( 'Name' ); ?></th>
                    <th align="center" style="width: 20%;"><?php echo get_lang( 'Id' ); ?></th>
                    <th align="center" style="width: 30%;"><?php echo get_lang( 'Secret key' ); ?></th>
                    <th align="center" style="width: 30%;"><?php echo get_lang( 'Redirect Uri' ); ?></th>
                    <th align="center"><?php echo get_lang( 'Delete' ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($this->clients as $client ) : ?>
                    <tr>
                        <td><?php echo $client['client_name']; ?></td>
                        <td><?php echo $client['client_id']; ?></td>
                        <td><?php echo $client['client_secret']; ?></td>
                        <td><?php echo $client['redirect_uri']; ?></td>
                        <td align="center">
                            <a class="claroCmd" href="<?php echo $_SERVER['PHP_SELF'] .'?cmd=Delete&amp;clientid=' . rawurlencode($client['client_id']); ?>">
                                <img src="<?php echo get_icon_url('delete'); ?>" alt="<?php echo get_lang('Delete'); ?>" />
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </fieldset>
    <fieldset class="collapsible collapsed">
        <legend><a href="#" class="doCollapse"><?php echo get_lang( 'Create new client' ); ?></a></legend>
        <div class="collapsible-wrapper">
            <dl>
                <dt>
                    <label for="client_name"><?php echo get_lang( 'Client Name' ); ?></label>
                    <span class="required">*</span>
                </dt>
                <dd>
                    <input type="text" required="required" name="client_name" id="client_name" value size="60" placeholder="Client Name">
                </dd>
                <dt>
                    <label for="client_name"><?php echo get_lang( 'Redirect Uri' ); ?></label>
                    <span class="required">*</span>
                </dt>
                <dd>
                    <input type="text" required="required" name="redirect_uri" id="redirect_uri" value size="60" placeholder="Redirect Uri (e.g. http://my.oauth.redirect)">
                </dd>
            </dl>
            <dl>
                <dt>
                    <input type="hidden" name="cmd" id="cmd" value="Create">
                    <input type="submit" name="create" value="<?php echo get_lang( 'Create' ); ?>">
                <p class="notice">
                    <span class="required">*</span> indique un champ obligatoire
                </p>
                </dt>
                <dd></dd>
            </dl>
        </div>
    </fieldset>
</form>
