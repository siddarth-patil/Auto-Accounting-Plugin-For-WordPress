<?php
function wpaSettings(){
    //currency symbol field,sales tax field, sales tax included in field
    $settings = array('wpaccounting_currency','wpaccounting_tax');
    
    if(!empty($_POST['settings'])){
        foreach($settings as $s){
            //if it is set then update it to zero
            update_option($s,(isset($_POST['settings'][$s]) ? $_POST['settings'][$s] : '0'));
        }
    }
    ?>
    <div class='wrap'>
        <div id="icon-edit-pages" class="icon32"><br /></div><h2><b>Wordpress Accounting Settings</b></h2>
        <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
        <table cellspacing="0" cellpadding="2" class="form-table">
            <tr>
                <th scope="row">Currency Symbol: </th>
                <td><input type="text" name="settings[wpaccounting_currency]" value="<?php echo get_option('wpaccounting_currency');?>"></td>
            </tr>
            <tr>
                <th scope="row">Sales Tax: </th>
                <td><input type="text" name="settings[wpaccounting_tax]" value="<?php echo get_option('wpaccounting_tax');?>">%</td>
            </tr>
            
        </table>
        <!-- save button -->
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p></form>
    </div>
    <?php
}
?>