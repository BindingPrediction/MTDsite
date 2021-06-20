cp $1 /home/sunzhe/MTDsites/run/
#cd /home/sunzhe/MTDsites/run/

/home/sunzhe/MTDsites/run/run_list.sh $1
cp $1.pssm /home/sunzhe/MTDsites/run/feature/
cp $1.hhm  /home/sunzhe/MTDsites/run/feature/
cp $1.spd33 /home/sunzhe/MTDsites/run/feature/

python3 /home/sunzhe/MTDsites/run/norm.py $1

python /home/sunzhe/MTDsites/run/pred.py $1

rm *.hhm *.spd33 *.pssm -f
