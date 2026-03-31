import torch
import torch.nn as nn
from torch.utils.data import Dataset, DataLoader
import os
import numpy as np
from scipy.io import wavfile

class AudioDataset(Dataset):
	def __init__(self, root: str):
		self.samples = []
		for label_name in ['real', 'fake']:
			label_dir = os.path.join(root, label_name)
			if not os.path.isdir(label_dir):
				continue
			for fname in os.listdir(label_dir):
				if fname.lower().endswith('.wav'):
					self.samples.append((os.path.join(label_dir, fname), 0 if label_name=='real' else 1))
	def __len__(self):
		return len(self.samples)
	def __getitem__(self, idx):
		path, label = self.samples[idx]
		rs, data = wavfile.read(path)
		data = data.astype(np.float32)
		if data.ndim > 1:
			data = data.mean(axis=1)
		# Pad/trim to 3s
		target_len = rs * 3
		if len(data) < target_len:
			pad = target_len - len(data)
			data = np.pad(data, (0, pad))
		else:
			data = data[:target_len]
		# Simple spectrogram
		import scipy.signal as s
		f, t, Sxx = s.spectrogram(data, fs=rs, nperseg=256, noverlap=128)
		Sxx = np.log(Sxx + 1e-6)
		Sxx = (Sxx - Sxx.mean()) / (Sxx.std() + 1e-6)
		Sxx = Sxx.astype(np.float32)
		return torch.from_numpy(Sxx).unsqueeze(0), label

class SmallCNN(nn.Module):
	def __init__(self):
		super().__init__()
		self.net = nn.Sequential(
			nn.Conv2d(1, 8, 3, padding=1), nn.ReLU(), nn.MaxPool2d(2),
			nn.Conv2d(8, 16, 3, padding=1), nn.ReLU(), nn.MaxPool2d(2),
			nn.AdaptiveAvgPool2d((1, 1))
		)
		self.fc = nn.Linear(16, 2)
	def forward(self, x):
		x = self.net(x)
		x = x.view(x.size(0), -1)
		return self.fc(x)


def train(data_root: str = './data', epochs: int = 1, batch_size: int = 8, lr: float = 1e-3, out: str = './audio_model.pt'):
	device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
	dataset = AudioDataset(os.path.join(data_root, 'train'))
	loader = DataLoader(dataset, batch_size=batch_size, shuffle=True, num_workers=2)
	model = SmallCNN().to(device)
	optim = torch.optim.Adam(model.parameters(), lr=lr)
	criterion = nn.CrossEntropyLoss()
	model.train()
	for epoch in range(epochs):
		for i, (x, y) in enumerate(loader):
			x = x.to(device)
			y = torch.tensor(y, dtype=torch.long, device=device)
			optim.zero_grad()
			logits = model(x)
			loss = criterion(logits, y)
			loss.backward()
			optim.step()
			if (i+1) % 10 == 0:
				print(f"epoch {epoch+1} step {i+1} loss {loss.item():.4f}")
	torch.save(model.state_dict(), out)
	print(f"saved to {out}")

if __name__ == '__main__':
	train()