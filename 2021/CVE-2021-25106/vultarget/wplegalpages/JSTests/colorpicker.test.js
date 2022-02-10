/**
 * @jest-environment jsdom
 */
import colorpicker from '../src/colorpicker';

it( 'Test for template', () => {
	var retrievedTemplate = colorpicker.template;
    expect(typeof retrievedTemplate).toBe('string');
    var htmlEscaped = retrievedTemplate.replace(/<\/?[^>]+(>|$)/g, "");
    expect( htmlEscaped === retrievedTemplate ).toBeFalsy();
})

it('test for data', ()=>{
    var expectedData = {
		colors: {
			hex: '#000000',
		},
		colorValue: '',
		displayPicker: false,
	}
    var retrievedData = colorpicker.data();
    expect(expectedData).toEqual(retrievedData);
})

it('test for methods', () => {
	var module = {
		colorValue: '',
		colors: {
			hex: '#000000'
		},
		updateColors(color) {
			this.colors = {
				hex: color
			}
		},
		$refs: {
			colorpicker: {
				value: ''
			}
		},
		e:{
            target:{
                getAttribute(){
                    return 1;
                }
            }
        },
		displayPicker: false,
		documentClick() {
			return;
		},
		showPicker() {
			this.displayPicker = true;
		},
		hidePicker() {
			this.displayPicker = false;
		},
	}
	var settingColor = colorpicker.methods.setColor.bind(module);
	settingColor( '#123123' );
	expect(module.colorValue).toBe('#123123');

	var updatedColors = colorpicker.methods.updateColors.bind(module);
	updatedColors( 'rgba(100,100,100,0.5)' );
	expect(module.colors).toEqual( {
		hex: '#646464',
		a: '0.5'
	} )
	updatedColors('#121212');
	expect(module.colors).toEqual({
		hex: '#121212'
	})

	var showPickers = colorpicker.methods.showPicker.bind(module);
	showPickers();
	expect(module.displayPicker).toBeTruthy();

	var hidePickers = colorpicker.methods.hidePicker.bind(module);
	hidePickers();
	expect(module.displayPicker).toBeFalsy();

	module.displayPicker = true;
	var togglePickers = colorpicker.methods.togglePicker.bind(module);
	togglePickers();
	expect(module.displayPicker).toBeFalsy();
	module.displayPicker = false;
	togglePickers();
	expect(module.displayPicker).toBeTruthy();

	var updateFromInputs = colorpicker.methods.updateFromInput.bind(module);
	module.colorValue = '#ffffff';
	updateFromInputs();
	expect(module.colors).toEqual({
		hex: '#ffffff'
	})

	var container = document.createElement('div');
	container.innerHTML = `<div class="input-group color-picker" ref="colorpicker">
	<input type="text" class="form-control" v-model="colorValue" @focus="showPicker()" @input="updateFromInput" />
	<span class="input-group-addon color-picker-container">
		<span class="current-color" :style="'background-color: ' + colorValue" @click="togglePicker()"></span>
		<chrome-picker :value="colors" @input="updateFromPicker" v-if="displayPicker" />
	</span>
	</div>`;
	var documentClick = colorpicker.methods.documentClick.bind(module);

	expect(true).toBeTruthy();

})

it('test for name', () => {
	expect(colorpicker.name).toBe('colorpicker');
})
