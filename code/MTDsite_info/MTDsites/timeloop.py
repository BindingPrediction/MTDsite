import time
import os
while True:
    time.sleep(5)
    f=open('/var/www/html/server/MTDsite/log/_queue.dat','r')
    l=f.readlines()
    f.close()
    f2=open('/var/www/html/server/MTDsite/log/_finished.dat','a+')
    f=open('/var/www/html/server/MTDsite/log/_queue.dat','w')
    if len(l)>0:
        print('begin')
        s= l[0].split(' ')
        name=s[2][:-1]
        print(name)
        path=os.getcwd()
        os.chdir(name)
        os.system(f'sh /home/sunzhe/MTDsites/run/run.sh query_fas')
        os.chdir(path)
        f.writelines(l[1:])
        f.close()
        f2.writelines(l[0])
        f2.close()
    else:
       f.close()
       f2.close()

    
