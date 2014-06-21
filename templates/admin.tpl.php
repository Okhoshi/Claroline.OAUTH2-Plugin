<form class="claroLoginForm" method="post">
    <fieldset>
        <legend><?php echo get_lang('Registered Clients') ?></legend>
        <table class="claroTable emphaseLine">
            <thead>
            <tr class="headerX">
                <th align="center" style="width: 30%;"><?php echo get_lang( 'Name' ); ?></th>
                <th align="center" style="width: 20%;"><?php echo get_lang( 'Id' ); ?></th>
                <th align="center" style="width: 20%;"><?php echo get_lang( 'Secret key' ); ?></th>
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
                    <td>
                        <a class="claroCmd" href="<?php echo $_SERVER['PHP_SELF'] .'?cmd=Delete&amp;clientid=' . rawurlencode($client['client_id']); ?>">
                            <img src="<?php echo get_icon_url('delete'); ?>" alt="<?php echo get_lang('Delete'); ?>" />
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </fieldset>
</form>
