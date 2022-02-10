<?php
/**
 * User: shahnuralam
 * Date: 18/11/18
 * Time: 10:13 PM
 */
if (!defined('ABSPATH')) die();
?>
<table style="width: 100%;<?php if(isset($css, $css['table'])) echo $css['table']; ?>" class="email <?php if(isset($tclass)) echo $tclass; ?>" cellspacing="0" cellpadding="0">
    <?php if(isset($thead)){ ?>
    <thead>
        <tr>
            <?php foreach ($thead as $th) { ?>
                <th style="<?php if(isset($css, $css['th'])) echo $css['th']; ?>"><?php echo $th; ?></th>
            <?php } ?>
        </tr>
    </thead>
    <?php } ?>
    <tbody>

        <?php $rn = 0; foreach ($data as $row){ ?>
            <tr>
                <?php $cn = 0; foreach ($row as $td) { ?>
                    <td style="<?php if(isset($css, $css['td'])) echo $css['td']; ?><?php if(isset($css, $css['col'], $css['col'][$cn])) echo $css['col'][$cn]; ?><?php if(isset($css, $css['row'], $css['row'][$rn])) echo $css['row'][$rn]; ?>"><?php echo $td; ?></td>
                <?php $cn++; } ?>
            </tr>
        <?php $rn++; } ?>

    </tbody>

</table>

