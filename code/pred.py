import numpy as np
import torch.nn as nn
import os
import sys
import torch


device = torch.device('cpu')

class RNN(nn.Module):
    def __init__(self,input_size, hidden_size, num_layers, num_classes):
        super(RNN, self).__init__()
        self.elu = nn.ELU(inplace=False)
        self.lstm = nn.LSTM(input_size, hidden_size, num_layers, batch_first=True,bidirectional=True)
        self.fc = nn.Linear(128, num_classes)
        self.dropout = torch.nn.Dropout(p=0.5,inplace=False)
        
    def forward(self, x):
        h0 = torch.zeros(6,x.permute(0,2,1).size(0),64).to(device)
        c0 = torch.zeros(6,x.permute(0,2,1).size(0),64).to(device)
        out, _ = self.lstm(x.permute(0,2,1),(h0,c0))

        out=out.permute(0,2,1)
        out = self.elu(out)
        out = self.dropout(out)
        o = self.fc(out.permute(0,2,1)).permute(0,2,1)
        return o
name = name=sys.argv[1]    
myname = 'final_'
path = '/home/sunzhe/MTDsites/run/npdata/'
path = path+name+'.npy'

f=np.load(path)
#f=np.transpose(f)
f=torch.FloatTensor(f)
f=f.unsqueeze(0)
f=f.to(device)

softm = torch.nn.Softmax(dim = 0)

modeld = RNN(54,64,3,2)
modeld = modeld.to(device)
modeld.load_state_dict(torch.load('/home/sunzhe/MTDsites/run/ck/'+myname+'15'+'.ckpt',map_location=torch.device('cpu')))
outputd = modeld(f)
scored = softm(outputd[0])
pred_d = scored[1].data.numpy().tolist()
for x in range(len(pred_d)):
        if float(pred_d[x])<0.196:
            pred_d[x]=1
        else: pred_d[x]=0




modelr = RNN(54,64,3,2)
modelr = modeld.to(device)
modelr.load_state_dict(torch.load('/home/sunzhe/MTDsites/run/ck/'+myname+'17'+'.ckpt',map_location=torch.device('cpu')))
outputr = modeld(f)
scorer = softm(outputr[0])
pred_r = scorer[1].data.numpy().tolist()
for x in range(len(pred_r)):
        if float(pred_r[x])<0.131:
            pred_r[x]=1
        else: pred_r[x]=0

modelp = RNN(54,64,3,2)
modelp = modeld.to(device)
modelp.load_state_dict(torch.load('/home/sunzhe/MTDsites/run/ck/'+myname+'25'+'.ckpt',map_location=torch.device('cpu')))
outputp = modeld(f)
scorep = softm(outputp[0])
pred_p = scorep[1].data.numpy().tolist()
for x in range(len(pred_p)):
        if float(pred_p[x])<0.268:
            pred_p[x]=1
        else: pred_p[x]=0

modelc = RNN(54,64,3,2)
modelc = modeld.to(device)
modelc.load_state_dict(torch.load('/home/sunzhe/MTDsites/run/ck/'+myname+'33'+'.ckpt',map_location=torch.device('cpu')))
outputc = modeld(f)
scorec = softm(outputc[0])
pred_c = scorec[1].data.numpy().tolist()
for x in range(len(pred_c)):
        if float(pred_c[x])<0.173:
            pred_c[x]=1
        else: pred_c[x]=0

f=open('./'+name+'.pred','w')

pred_d=[str(x) for x in pred_d]
pred_r=[str(x) for x in pred_r]
pred_p=[str(x) for x in pred_p]
pred_c=[str(x) for x in pred_c]

f.write(name)
f.write('\n')

f.write('DNA Binding sites:  ')
f.writelines(pred_d)
f.write('\n')

f.write('RNA Binding sites:  ')
f.writelines(pred_r)
f.write('\n')

f.write('Peptide Binding sites:  ')
f.writelines(pred_p)
f.write('\n')

f.write('Carbohydrate Binding sites:  ')
f.writelines(pred_c)
f.write('\n')

f.close()
