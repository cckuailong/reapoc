<?php
	$promoLink = $this->promoLink. '?utm_source=plugin&utm_campaign=ultimatemaps&utm_medium='. $this->tabCode;
?>
<a target="_blank" class="button button-primary" href="<?php echo $promoLink?>">
<?php _e('Get it now!')?>
</a>
<a target="_blank" href="<?php echo $promoLink?>">
	<img src="<?php echo $this->getModule()->getModPath(). 'img/'. $this->tabCode. '.jpg'?>" />
</a>
