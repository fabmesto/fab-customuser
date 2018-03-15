<div class="wrap">
  <h1>FAB Custom User</h1>

  <?php settings_errors(); ?>

  <form method="post" action="options.php">
    <?php settings_fields( 'fabcustomuser-options' );
    do_settings_sections( 'fabcustomuser-options' );
    ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Ogni (n°) paragrafo</th>
        <td><input type="text" name="every_n_p" value="<?php echo esc_attr( get_option('every_n_p') ); ?>" style="width:100%" /></td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>
</div>
