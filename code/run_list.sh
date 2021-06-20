#!/bin/bash

### set these four variables correctly ####
NR90=/home/sunzhe/MTDsites/run/uniref90/uniref90
HHDB=/home/sunzhe/MTDsites/run/uniclust30_2017_10/uniclust30_2017_10
HHBLITS=/home/sunzhe/MTDsites/run/hhsuite3.0/bin/hhblits
psiblast=/home/sunzhe/MTDsites/run/blast-2.7.1+/bin/
###

#
PDIR=$(dirname $0)
xdir=/home/sunzhe/MTDsites/run/SPIDER3-numpy-server
ncpu=$OMP_NUM_THREADS
if [ "$ncpu" == "" ]; then ncpu=16; fi
if [ $# -lt 1 ]; then echo "usage: $0 *.[seq|pssm]"; exit 1; fi

for seq1 in $(shuf -e $*); do
	pro0=$(basename $seq1)
	pro1=$(basename $(basename $seq1 .seq) .pssm)
	[ -f $pro1.spd33 -o -f $pro0.spd33 ] && continue
	if [ ! -f $pro1.pssm -a ! -f $pro1.bla ]; then
		if [ ! -f $HHDB.cs219 ]; then echo "HHDB not set: $HHDB.cs219 not exists"; exit 1; fi
		if [ ! -f $NR90.pal ]; then "NR90 not set: $NR90.pal not exists"; exit 1; fi
#
		$psiblast/psiblast -db $NR90 -num_iterations 3 -num_alignments 1 -num_threads $ncpu -query $seq1 -out  $pro1.bla -out_ascii_pssm ./$pro1.pssm #-out_pssm ./$pro1.chk
		[ ! -f $pro1.pssm ] && $xdir/script/seq2pssm.py $seq1 > $pro1.pssm  # using blosum when failed
		$HHBLITS -i $seq1 -ohhm $pro1.hhm -d $HHDB -v0 -maxres 40000 -cpu $ncpu -Z 0 -o $pro1.hhr
		[ -f $pro1.hhm -a -f $pro1.pssm  ] && rm -f $pro1.hhr $pro1.bla
	fi
done
#
$xdir/script/spider3_pred.py $*
