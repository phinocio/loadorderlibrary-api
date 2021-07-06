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
				Every route below is relative to <code>{{config('app.url')}}/v1</code>
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

						<h4>Example Response</h4>
						<pre class="code p-2 position-relative"><button type="button" class="btn btn-primary btn-sm text-white position-absolute top-0 end-0 mt-3 me-2" onclick="copyAddress('listGetExample')">Copy</button><code id="listGetExample">{
    "data": [
        {
            "name": "My New List! Again",
            "slug": "my-new-list-again",
            "private": 0,
            "created": "2021-07-03T02:50:16.000000Z",
            "updated": "2021-07-03T02:50:16.000000Z",
            "author": null,
            "game": {
                "id": 1,
                "name": "TESIII Morrowind"
            },
            "files": [
                {
                    "name": "9f87e14d8aa888362776f88f995d08cd-Skyrim.ini",
                    "clean_name": "Skyrim.ini",
                    "bytes": 3117,
                    "created": "2021-07-02T22:55:55.000000Z",
                    "updated": "2021-07-02T22:55:55.000000Z"
                },
                {
                    "name": "00acfcec29588e25041c76307b860016-modlist.txt",
                    "clean_name": "modlist.txt",
                    "bytes": 8420,
                    "created": "2021-07-02T22:55:55.000000Z",
                    "updated": "2021-07-02T22:55:55.000000Z"
                },
                {
                    "name": "04ff9e544df36ef076bd26e8f42aeee4-plugins.txt",
                    "clean_name": "plugins.txt",
                    "bytes": 6404,
                    "created": "2021-07-02T22:55:55.000000Z",
                    "updated": "2021-07-02T22:55:55.000000Z"
                }
            ]
        }
    ],
    "links": {
        "first": "{{config('app.url')}}/v1/lists?sort=-created&page=1",
        "last": "http://api.loadorderlibrary.localhosdsfdsfdsfdst/api/lists?sort=-created&page=4",
        "prev": null,
        "next": "http://api.loadorderlibrary.localhost/api/lists?sort=-created&page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 4,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://api.loadorderlibrary.localhost/api/lists?sort=-created&page=1",
                "label": "1",
                "active": true
            },
            {
                "url": "http://api.loadorderlibrary.localhost/api/lists?sort=-created&page=2",
                "label": "2",
                "active": false
            },
            {
                "url": "http://api.loadorderlibrary.localhost/api/lists?sort=-created&page=3",
                "label": "3",
                "active": false
            },
            {
                "url": "http://api.loadorderlibrary.localhost/api/lists?sort=-created&page=4",
                "label": "4",
                "active": false
            },
            {
                "url": "http://api.loadorderlibrary.localhost/api/lists?sort=-created&page=2",
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://api.loadorderlibrary.localhost/api/lists",
        "per_page": 1,
        "to": 1,
        "total": 4
    }
}
						</code></pre>
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
</script>