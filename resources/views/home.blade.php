@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card bg-dark mb-5">
			<div class="card-header text-large">
				<h3>Load Order Library API Documentation</h3>
			</div>
			<div class="card-body">
				<p>Every route below is relative to <code>{{config('app.url')}}/v1</code>.</p>
				<p>Send requests with <code>accepts: application/json</code> header.</p>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<h3>Lists</h3>
		<div class="accordion accordian-flush mb-5" id="listsAccordian">
			<div class="accordion-item bg-dark">
				<h2 class="accordion-header" id="listsGetHeading">
					<button class="accordion-button bg-dark text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#listsGetCollapse" aria-expanded="false" aria-controls="listsGetCollapse">
						<span class="badge bg-success me-2">GET</span> <span class="route">/lists</span>
					</button>
				</h2>
				<div id="listsGetCollapse" class="accordion-collapse collapse show" aria-labelledby="listsGetHeading" data-bs-parent="#listsAccordian">
					<div class="accordion-body">
						<p class="text">By default, lists are returned paginated with 14 per page.</p>
						<p class="text">Returns <code>HTTP 200</code> on successful request and <code>HTTP 422</code> if the request is malformed (author or game doesn't exist, for example).</p>
						<p class="text">There are a few query parameter options to filter/sort the results. Subsequent queries can be chained by replacing <code>?</code> with <code>&</code>.</p>

						<table class="table text-white">
							<thead>
								<tr>
									<th scope="col">Query Param</th>
									<th scope="col">Valid Options</th>
									<th scope="col">Example</th>
									<th scope="col">Description</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><span style="color:#f66d9b">filter[author]</span></td>
									<td>Any user's name</td>
									<td style="color:#f66d9b">?filter[author]=phinocio</td>
									<td>Filters the results by the author name</td>
								</tr>
								<tr>
									<td><span style="color:#f66d9b">filter[game]</span></td>
									<td>One of the games supported</td>
									<td style="color:#f66d9b">?filter[game]=TESIII Morrowind</td>
									<td>Filters the results by the game name</td>
								</tr>
								<tr>
									<td><span style="color:#f66d9b">sort</span></td>
									<td>updated | created</td>
									<td style="color:#f66d9b">?sort=-updated</td>
									<td>Sorts the results by the updated or created. Prepending <code>-</code> sorts newest first.</td>
								</tr>
							</tbody>
						</table>

						<h5 class="d-inline me-3">Example Response</h5><small><code>/lists?filter[author]=phinocio&filter[game]=TESIII Morrowind&sort=-created</code></small>
						<pre class="code p-2 position-relative">
							<button type="button" class="btn btn-primary btn-sm text-white position-absolute top-0 end-0 mt-3 me-2" onclick="copyAddress('listGetExample')">Copy</button>
<code id="listGetExample">{
    "data": [
        {
            "name": "A Third Morrowind List!",
            "version": "2.69.0",
            "slug": "a-third-morrowind-list",
            "url": "https://loadorderlibrary.com/lists/a-third-morrowind-list",
            "private": 0,
            "created": 1625604170,
            "updated": 1625604170,
            "author": {
                "name": "Phinocio"
            },
            "game": {
                "id": 1,
                "name": "TESIII Morrowind"
            },
            "files": [
                {
                    "name": "f8456ddcaf77ee434325eeda2af6e032-loadorder.txt",
                    "clean_name": "loadorder.txt",
                    "bytes": 3341,
                    "created": 1624752076,
                    "updated": 1624752076
                }
            ]
        },
        {
            "name": "Morrowind List 2",
            "version": null,
            "slug": "morrowind-list-2",
            "url": "https://loadorderlibrary.com/lists/morrowind-list-2",
            "private": 0,
            "created": 1625604151,
            "updated": 1625604151,
            "author": {
                "name": "Phinocio"
            },
            "game": {
                "id": 1,
                "name": "TESIII Morrowind"
            },
            "files": [
                {
                    "name": "00acfcec29588e25041c76307b860016-modlist.txt",
                    "clean_name": "modlist.txt",
                    "bytes": 8420,
                    "created": 1624752076,
                    "updated": 1624752076
                },
                {
                    "name": "6a2c0014a27ebdd573bf5634a78e6ced-plugins.txt",
                    "clean_name": "plugins.txt",
                    "bytes": 3405,
                    "created": 1624752076,
                    "updated": 1624752076
                }
            ]
        },
        {
            "name": "A morrowind List!!!",
            "version": "1.0.0",
            "slug": "a-morrowind-list-1",
            "url": "https://loadorderlibrary.com/lists/a-morrowind-list-1",
            "private": 0,
            "created": 1625604128,
            "updated": 1625604128,
            "author": {
                "name": "Phinocio"
            },
            "game": {
                "id": 1,
                "name": "TESIII Morrowind"
            },
            "files": [
                {
                    "name": "f8456ddcaf77ee434325eeda2af6e032-loadorder.txt",
                    "clean_name": "loadorder.txt",
                    "bytes": 3341,
                    "created": 1624752076,
                    "updated": 1624752076
                },
                {
                    "name": "00acfcec29588e25041c76307b860016-modlist.txt",
                    "clean_name": "modlist.txt",
                    "bytes": 8420,
                    "created": 1624752076,
                    "updated": 1624752076
                },
                {
                    "name": "6a2c0014a27ebdd573bf5634a78e6ced-plugins.txt",
                    "clean_name": "plugins.txt",
                    "bytes": 3405,
                    "created": 1624752076,
                    "updated": 1624752076
                }
            ]
        }
    ],
    "links": {
        "first": "http://api.loadorderlibrary.localhost/v1/lists?filter%5Bauthor%5D=phinocio&filter%5Bgame%5D=TESIII%20MoRRowind&sort=-created&page=1",
        "last": "http://api.loadorderlibrary.localhost/v1/lists?filter%5Bauthor%5D=phinocio&filter%5Bgame%5D=TESIII%20MoRRowind&sort=-created&page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://api.loadorderlibrary.localhost/v1/lists?filter%5Bauthor%5D=phinocio&filter%5Bgame%5D=TESIII%20MoRRowind&sort=-created&page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://api.loadorderlibrary.localhost/v1/lists",
        "per_page": 14,
        "to": 3,
        "total": 3
    }
}
							</code>
						</pre>
					</div>
				</div>
			</div>

			<div class="accordion-item bg-dark">
				<h2 class="accordion-header" id="listsPostHeading">
					<button class="accordion-button bg-dark text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#listsPostCollapse" aria-expanded="false" aria-controls="listsPostCollapse">
						<span class="badge bg-info me-2">POST</span> <span class="route">/lists</span>
					</button>
				</h2>
				<div id="listsPostCollapse" class="accordion-collapse collapse" aria-labelledby="listsPostHeading" data-bs-parent="#listsAccordian">
					<div class="accordion-body">
						Body Text
					</div>
				</div>
			</div>
		</div>

		<h3>Compare</h3>
		<div class="accordion accordian-flush" id="compareAccordian">
			<div class="accordion-item bg-dark">
				<h2 class="accordion-header" id="compareGetHeading">
					<button class="accordion-button bg-dark text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#compareGetCollapse" aria-expanded="false" aria-controls="compareGetCollapse">
						<span class="badge bg-success me-2">GET</span> <span class="route">/compare</span>
					</button>
				</h2>
				<div id="compareGetCollapse" class="accordion-collapse collapse" aria-labelledby="compareGetHeading" data-bs-parent="#compareAccordian">
					<div class="accordion-body">

					</div>
				</div>
			</div>

			<div class="accordion-item bg-dark">
				<h2 class="accordion-header" id="comparePostHeading">
					<button class="accordion-button bg-dark text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#comparePostCollapse" aria-expanded="false" aria-controls="comparePostCollapse">
						<span class="badge bg-info me-2">POST</span> <span class="route">/compare</span>
					</button>
				</h2>
				<div id="comparePostCollapse" class="accordion-collapse collapse" aria-labelledby="comparePostHeading" data-bs-parent="#compareAccordian">
					<div class="accordion-body">
						Body Text
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


<script>
	function copyAddress(target) {
		const address = document.getElementById(target).innerText;
		navigator.clipboard.writeText(address);
	}

	function getListsExample() {
		fetch('/v1/lists?filter[author]=phinocio&filter[game]=TESIII Morrowind&sort=-updated')
			.then(response => response.json())
			.then(data => {
				console.log(JSON.stringify(data.data[0]));
				document.getElementById('listGetExample').innerHTML = JSON.stringify(data.data[0]);
			});
	}
</script>