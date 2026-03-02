@if(session()->has('message'))

<div x-data="{show : true}" x-show="show" x-init="setTimeout(()=> show = false, 5000)" class="p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-green-700/50" role="alert">
  <span class="font-medium text-white">Alert Message</span> <h1>{{session('message')}}</h1>
</div>

@endif