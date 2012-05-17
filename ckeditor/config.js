if (typeof CKCONFIG == 'undefined')
{
	var CKCONFIG = {};
}

CKEDITOR.on('instanceReady', function(e)
{
	var writer = e.editor.dataProcessor.writer;
	var opt = {
		indent          : false,
		breakBeforeOpen : true,
		breakAfterOpen  : false,
		breakBeforeClose: false, 
		breakAfterClose : true
	};
	var tags = 'p,div,pre,ul,ol,li,h1,h2,h3,h4,h5,h6'.split(',');
	for (var i=0; i<tags.length; i++) writer.setRules(tags[i], opt);

	CKCONFIG.default_header_tag = CKCONFIG.default_header_tag || 'h2';

	// My cleaner
	e.editor.on('paste', function(e) {
		var editor = e.editor;
		var html   = e.data.html;

		e.stop();
		
		html = html.replace(/<[\/]?span[^>]*>/ig, '');
		html = html.replace(/[\s]*(class|style)=(["']?)[^>\2]+\2?/ig, '');
		html = html.replace(/<p>(<br[ \/]*>)?<\/p>/ig, '');
		html = html.replace(/<p><b>([^<]+)<\/b>[:.;]?(.?)<\/p>/ig, '<'+CKCONFIG.default_header_tag+'>$1$2</'+CKCONFIG.default_header_tag+'>');
		
		editor.insertHtml(html);

		// SELECT ALL & remove format
		// var range = new CKEDITOR.dom.range( editor.document );
		// range.selectNodeContents( editor.document.getBody() );
		// range.select();
		// editor.execCommand('removeFormat', editor.selection);
	});
});


CKEDITOR.editorConfig = function(config)
{
	config.extraPlugins = 'foton';

	// Define changes to default configuration here. For example:
	config.language = 'ru';
	config.uiColor  = CKCONFIG.ui_color || '#CCC';
	config.dialog_backgroundCoverColor = '#000';	

	config.enterMode = CKEDITOR.ENTER_P;
	config.autoParagraph = false;
	config.fillEmptyBlocks = false;
	config.ignoreEmptyParagraph = true;
	config.forcePasteAsPlainText = false;
	config.pasteFromWordNumberedHeadingToList = true;
	config.pasteFromWordPromptCleanup = true;
	config.pasteFromWordRemoveFontStyles = true;
	config.pasteFromWordRemoveStyles = true;
	config.startupOutlineBlocks = true;
	
	config.filebrowserUploadUrl = '/admin/uploader';
	config.contentsCss = CKCONFIG.content_css || '/res/css/global.css';
	config.height      = CKCONFIG.editor_height || 350;
	
	config.keystrokes = [
	   [ CKEDITOR.ALT + 192 /*`*/, 'foton-p' ],
	   [ CKEDITOR.ALT + 49 /*1*/, 'foton-h1' ],
	   [ CKEDITOR.ALT + 50 /*2*/, 'foton-h2' ],
	   [ CKEDITOR.ALT + 51 /*3*/, 'foton-h3' ],
	   [ CKEDITOR.ALT + 52 /*4*/, 'foton-h4' ],
	   [ CKEDITOR.ALT + 53 /*5*/, 'foton-h5' ]
	];

	config.toolbar_Full =
	[
		{ name: 'document',    items : [ 'Source'] },
		{ name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord'] },
		{ name: 'undoredo',    items : ['Undo','Redo' ] },
		// { name: 'editing',     items : [ 'Find','Replace'] },
		{ name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
		{ name: 'insert',      items : [ 'Image','Flash','Table','HorizontalRule','SpecialChar'] },
		{ name: 'tools',       items : [ 'ShowBlocks', 'Maximize' ] },

		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'foton',      items : [ 'foton-p', 'foton-h1', 'foton-h2', 'foton-h3', 'foton-h4', 'foton-h5'/*, '-', 'foton-wand'*/] },
		// { name: 'styles',      items : [ 'Format','FontSize' ] },
		// { name: 'colors',      items : [ 'TextColor','BGColor' ] },
		{ name: 'links',       items : [ 'Link','Unlink','Anchor']}
	];
};


