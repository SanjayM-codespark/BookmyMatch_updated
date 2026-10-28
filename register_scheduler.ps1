$action  = New-ScheduledTaskAction -Execute 'C:\xampp8.3\php\php.exe' -Argument 'C:\xampp8.3\htdocs\bookmymatch\spark tasks:run'
$trigger = New-ScheduledTaskTrigger -RepetitionInterval (New-TimeSpan -Minutes 1) -Once -At (Get-Date)
$settings = New-ScheduledTaskSettingsSet -ExecutionTimeLimit (New-TimeSpan -Minutes 1) -MultipleInstances IgnoreNew
Register-ScheduledTask -TaskName 'BookMyMatch Scheduler' -Action $action -Trigger $trigger -Settings $settings -RunLevel Highest -Force
Write-Host "Done! BookMyMatch Scheduler is registered and will run every minute." -ForegroundColor Green
