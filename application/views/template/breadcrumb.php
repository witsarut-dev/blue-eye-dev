<div ng-controller="breadcrumbCtrl">
  <ol class="breadcrumb" >
      <li><a href="webadmin"><span class="fa fa-home"></span> Home</a></li> 
      <li ng-repeat="item in breadcrumb" class="{{item.active=='true' ? 'active' : ''}}">
        <span  ng-if="item.active == 'true'" >{{item.label}}</span>
        <a ng-if="item.active != 'true'" href="{{item.link}}">{{item.label}}</a>
      </li>
  </ol>
</div>