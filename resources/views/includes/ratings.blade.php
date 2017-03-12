@if ($ratings >= 8)
	<i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i>
@elseif ($ratings >= 7)
	<i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i>
@elseif ($ratings >= 6)
	<i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i>
@elseif ($ratings >= 5)
	<i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi"></i><i class="fa fa-star-oi"></i>
@elseif ($ratings >= 4)
	<i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi"></i>
@else
	<i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i><i class="fa fa-star-oi desactivate"></i>
@endif
