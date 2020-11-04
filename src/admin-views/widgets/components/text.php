<?php
/**
 * Admin View: Widget Text Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/widgets/components/text.php
 *
 * See more documentation about our views templating system.
 *
 * @link    http://m.tri.be/1aiy
 *
 * @version TBD
 *
 * @var string $label Label for the text input.
 * @var string $value Value for the text input.
 * @var string $id    ID of the text input.
 * @var string $name  Name attribute for the text input.
 */

?>
<p
		class="tribe-widget-text tribe-common-form-control-text"
>
	<label
			class="tribe-common-form-control-text__label"
			for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
	<input
			class="tribe-common-form-control-text__input widefat"
			id="<?php echo esc_attr( $id ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			type="text"
			value="<?php echo esc_attr( $value ); ?>"
	/>
</p>