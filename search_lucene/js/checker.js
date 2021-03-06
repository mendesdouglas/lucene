function luceneIndexFiles() {
	var spinner, count, updateEventSource;
	if (luceneIndexFiles.active) {
		return;
	}
	t('search_lucene', 'Indexing... {count} files left', {count: 0}); //preload translations
	luceneIndexFiles.active = true;
	updateEventSource = new OC.EventSource(OC.generateUrl('/apps/search_lucene/indexer/index', {}));
	updateEventSource.listen('count', function (unIndexedCount) {
		count = unIndexedCount;
		if (count > 0) {
			spinner = $('form.searchbox #spinner .icon-loading');
			if (spinner.length === 0) {
				$('#searchbox').addClass('indexing');
				spinner = $('<div id="spinner"/>');
				$('form.searchbox').append(spinner);
				spinner.tipsy({trigger: 'manual', gravity: 'e', fade: false});
				spinner.attr('title', t('search_lucene', 'Indexing... {count} files left', {count: count}));
				spinner.tipsy('show');
			}
		}
	});

	updateEventSource.listen('error', function (path, e, a) {
		console.log('error while indexing ' + path);
		console.log(e);
		console.log(a);
	});

	updateEventSource.listen('indexing', function (path) {
		count--;
		spinner.attr('title', t('search_lucene', 'Indexing... {count} files left', {count: count}));
		spinner.tipsy('show');
	});

	updateEventSource.listen('done', function (path) {
		if (spinner) {
			spinner.tipsy('hide');
			spinner.remove();
		}
	});
}
luceneIndexFiles.active = false;

$(document).ready(function () {
	//add listener to the search box
	$('#searchbox').on('click', function () {
		setTimeout(function () { //load other stuff first
			//check status of indexer
			luceneIndexFiles();
		}, 100);
	});

	//clock that shows progress ○◔◑◕●.
	//hovering over it shows the current file
	//clicking it stops the indexer: ⌛

	OC.search.resultTypes.lucene = t('search_lucene', 'In');

	OC.search.customResults.lucene = function ($row, item){
		$row.find('td.result .text').text(t('search_lucene', 'Score: {score}', {score: Math.round(item.score*100)/100}));
	};

});
