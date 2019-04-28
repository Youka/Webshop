function enableTableSort() {
	// For each table header with 'sortable' class
	$('th.sortable').each(function(i, elem) {
		// Table header element as jQuery object
		var th = $(elem);
		// Add click action
		th.on('click', function() {
			// State
			var tbody = $(elem).parent().parent().next(),	// th -> tr -> thead -> tbody
				bodyRows = tbody.children('tr').toArray(),	// Table body rows
				colIndex = th.index(),	// Column index
				desc = th.hasClass('desc');	// Sort method
			// Sort rows
			bodyRows.sort(function(prev, cur) {
				// Compare rows by cell html content
				var a = $(prev).children('td').get(colIndex).innerHTML,
					b = $(cur).children('td').get(colIndex).innerHTML;
				// Convert string to number if possible
				if(isFinite(a)) a = parseFloat(a);
				if(isFinite(b)) b = parseFloat(b);
				// Compare considering ordering
				return (a > b ? 1 : -1) * (desc ? -1 : 1);
			});
			// Replace old rows by sorted ones
			tbody.empty();
			for(var i = 0; i < bodyRows.length; i++)
				tbody.append(bodyRows[i]);
			// Update header markers
			th.parent().children('th').removeClass('asc desc');
			th.addClass(desc ? 'asc' : 'desc');
		});
	});
}