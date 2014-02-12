#! /bin/sh
# ./pseudocron.sh /var/www/apache2-default/workspace/conveyorbranch/publish/eft_test.php 60000 specified 10&
curdir=`pwd`
command=${1##/*/}
comdir=${1%/*.php}
wtime=$2
option=${3##-}
shift 3
if [ $option != "none" ]; then
loop=$1
shift
fi
cd $comdir
l=0
wl=0
finished=0
while [ $finished -lt 1 ]
  do
  if [ "$1" != "" ]; then
    echo 'option exists'
    for opt in "$@"
      do
      php $command $opt &
      l=`expr $l + 1`
      sleep $wtime
    done
  else
    php $command &
    l=`expr $l + 1`
    echo 'passed'
    sleep $wtime
  fi
  wl=`expr $wl + 1`
  echo $l
  echo $wl
  if [ $option = "specified" ]; then
    if [ $l -ge $loop ]; then
      finished=1
      echo 'finish'
    fi
  elif [ $option = "wholespecified" ]; then
    if [ $wl -ge $loop ]; then
      finished=1
    fi
  fi
done
cd $curdir
