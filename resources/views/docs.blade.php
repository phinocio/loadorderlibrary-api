@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="row">
	<div class="col-md-3">
		<div class="card bg-dark mb-5">
			<div class="card-header text-large">
				<h3>API Documentation</h3>
			</div>
			<div class="card-body p-0">
				<ul class="list-group p-0 m-0">
					<li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center">
						<span style="color:#f66d9b">/lists</span>
						<span class="badge bg-success rounded-pill">GET</span>
					</li>
					<li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center">
						<span style="color:#f66d9b">/lists</span>
						<span class="badge bg-info rounded-pill">POST</span>
					</li>
					<li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center">
						A third list item
						<span class="badge bg-primary rounded-pill">1</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-9">

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