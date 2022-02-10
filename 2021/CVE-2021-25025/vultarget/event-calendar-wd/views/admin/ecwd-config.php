<?php
if ($response) {
    foreach ($response as $text) {
        var_dump($response);
    }
}
?>
<form method="post" action="<?php echo $action; ?>">
    <table style="width:100%;">
        <tbody>
            <tr>
                <td><?php _e('Name','event-calendar-wd');?></td>
                <td><?php _e('Description','event-calendar-wd');?></td>
                <td style="width:20%;"><?php _e('Value','event-calendar-wd');?></td>
            </tr>
            <?php
            foreach ($configs as $id => $conf) {
                ?>  
                <tr>                
                    <td><?php echo $conf['name']; ?></td>                
                    <td><?php echo $conf['description']; ?></td>                
                    <td>
                        <input type="text" name="<?php echo $id; ?>" 
                               value="<?php echo $conf['value'] ?>" />
                    </td>                                
                </tr>
                <?php
            }
            ?>        
        </tbody>
    </table>
    <input type="submit" value="Save" />
</form>
