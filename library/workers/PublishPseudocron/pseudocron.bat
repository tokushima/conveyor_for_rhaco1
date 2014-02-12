@rem ./pseudocron.bat C:\home\workspace\conveyorbranch\publish\eft_test.php 60000 specified 10&
@setlocal ENABLEDELAYEDEXPANSION
@set curdir=%CD%
@set command=%~n1.php
@set comdir=%~p1
@set wtime=%2
@set option=%3
@shift
@shift
@shift
@rem for %%f in (*.vbs) do 
@echo WScript.sleep %wtime%*1000 > sleep.vbs
@if not %option%.==none. (
@set loop=%1
@shift
)
@rem #other parameters
@set c=0
:param
@if not %1.==. (
@set /a c=%c%+1
@set p!c!=%1
@shift
@goto :param
)
@cd %comdir%
@set  l=0
@set  wl=0
@set finished=0
:while
  @if %c% gtr 0 (
    @for /l %%o in (1,1,%c%) do @(
      @php %command% !p%%o!
      @set /a l=%l%+1
      @C:\WINDOWS\system32\cscript.exe sleep.vbs  > NUL
    )
  ) else (
    @php %command%
    @set /a l=%l%+1
    @C:\WINDOWS\system32\cscript.exe sleep.vbs  > NUL
  )
  @set /a wl=%wl%+1
  @if %option%.==specified. (
    @if %l% geq %loop% (
      @set finished=1
    )
  ) else ( @if %option%.==wholespecified. (
    @if %wl% geq %loop% (
      @set finished=1
    )
  ))
@if not %finished%==1 goto :while
@cd %curdir%
@del sleep.vbs
@endlocal