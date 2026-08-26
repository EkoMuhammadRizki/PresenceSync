$WshShell = New-Object -ComObject WScript.Shell

$icoPath = "c:\File Eko\PresenceSync\logo\Siap_Logo.ico"
$exePath = "c:\File Eko\PresenceSync\SIAP-PresenceSync.exe"

$locations = @(
    [Environment]::GetFolderPath('Desktop'),
    "C:\Users\ekomu\Desktop",
    "C:\Users\ekomu\OneDrive\Desktop",
    "$env:APPDATA\Microsoft\Internet Explorer\Quick Launch\User Pinned\TaskBar"
) | Select-Object -Unique

foreach ($loc in $locations) {
    if (Test-Path $loc) {
        # 1. Update Shortcut .lnk
        $shortcutPath = Join-Path $loc "SIAP PresenceSync.lnk"
        $shortcut = $WshShell.CreateShortcut($shortcutPath)
        $shortcut.TargetPath = $exePath
        $shortcut.WorkingDirectory = "c:\File Eko\PresenceSync"
        $shortcut.IconLocation = "$icoPath,0"
        $shortcut.Description = "Sistem Informasi Absensi Presensi (SIAP) - PresenceSync"
        $shortcut.Save()
        Write-Host "Updated LNK at: $shortcutPath"

        # Taskbar specific link name if exists
        $tbPath = Join-Path $loc "SIAP-PresenceSync.lnk"
        if (Test-Path $tbPath) {
            $shortcut = $WshShell.CreateShortcut($tbPath)
            $shortcut.TargetPath = $exePath
            $shortcut.WorkingDirectory = "c:\File Eko\PresenceSync"
            $shortcut.IconLocation = "$icoPath,0"
            $shortcut.Description = "Sistem Informasi Absensi Presensi (SIAP) - PresenceSync"
            $shortcut.Save()
            Write-Host "Updated Taskbar LNK at: $tbPath"
        }

        # 2. Update Shortcut .url
        if ($loc -notmatch "TaskBar") {
            $urlShortcutPath = Join-Path $loc "SIAP PresenceSync.url"
            $urlContent = "[InternetShortcut]`nURL=https://siapsman1ciparay.test/`nIconFile=$icoPath`nIconIndex=0"
            $urlContent | Out-File -FilePath $urlShortcutPath -Encoding ascii -Force
            Write-Host "Updated URL shortcut at: $urlShortcutPath"
        }
    }
}
