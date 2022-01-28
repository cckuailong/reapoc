<?php

use ProfilePress\Core\ContentProtection\ContentConditions;

$dbData = $contentToRestrictData

?>
<div class="pp-content-protection-content-box">
    <section id="ppContentProtectionContent">
        <div id="workflowConditions">
            <?php if (is_array($dbData) && ! empty($dbData)): $index = 0; $count = count($dbData); ?>
                <?php foreach ($dbData as $facetListId => $facets) : ++$index; ?>
                    <?php ContentConditions::get_instance()->rules_group_row($facetListId, '', $facets, $index !== $count); ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ( ! is_array($dbData) || empty($dbData)): ?>
                <?php ContentConditions::get_instance()->rules_group_row(
                    wp_generate_password(18, false),
                    wp_generate_password(18, false)
                ); ?>
            <?php endif; ?>
    </section>
</div>