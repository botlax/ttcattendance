

<p style="font-size=20px"><strong>Attendance sheet have been populated for:</strong></p>
<br>
<p>Date: <span style="color:blue">{{$date}}</span></p>
<p>By: <span style="color:blue">{{$siteInCharge}}</span></p>
<p>At: <span style="color:blue">
@foreach($sites as $site)
{{$site->code}},
@endforeach
</span></p>

<p>Check entries here: <a href="">{{$link}}</a></p>

