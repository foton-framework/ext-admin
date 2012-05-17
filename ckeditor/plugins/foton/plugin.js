CKEDITOR.plugins.add( 'foton',
{
	init: function( editor )
	{
		editor.addCommand('foton-p', {exec: function(editor) {(new CKEDITOR.style({element: 'p'})).apply(editor.document)}});
		editor.addCommand('foton-h1', {exec: function(editor) {(new CKEDITOR.style({element: 'h1'})).apply(editor.document)}});
		editor.addCommand('foton-h2', {exec: function(editor) {(new CKEDITOR.style({element: 'h2'})).apply(editor.document)}});
		editor.addCommand('foton-h3', {exec: function(editor) {(new CKEDITOR.style({element: 'h3'})).apply(editor.document)}});
		editor.addCommand('foton-h4', {exec: function(editor) {(new CKEDITOR.style({element: 'h4'})).apply(editor.document)}});
		editor.addCommand('foton-h5', {exec: function(editor) {(new CKEDITOR.style({element: 'h5'})).apply(editor.document)}});
		editor.ui.addButton('foton-p', {label:'P', command:'foton-p', icon:this.path + 'images/edit-pilcrow.png'});
		editor.ui.addButton('foton-h1', {label:'H1', command:'foton-h1', icon:this.path + 'images/edit-heading-1.png'});
		editor.ui.addButton('foton-h2', {label:'H2', command:'foton-h2', icon:this.path + 'images/edit-heading-2.png'});
		editor.ui.addButton('foton-h3', {label:'H3', command:'foton-h3', icon:this.path + 'images/edit-heading-3.png'});
		editor.ui.addButton('foton-h4', {label:'H4', command:'foton-h4', icon:this.path + 'images/edit-heading-4.png'});
		editor.ui.addButton('foton-h5', {label:'H5', command:'foton-h5', icon:this.path + 'images/edit-heading-5.png'});
	}
} );