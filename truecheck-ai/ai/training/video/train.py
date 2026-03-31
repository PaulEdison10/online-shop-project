import torch
import torch.nn as nn
from torch.utils.data import Dataset, DataLoader
import os
import cv2
import numpy as np

class VideoFrameDataset(Dataset):
	def __init__(self, root: str, frames_per_video: int = 4):
		self.samples = []
		self.frames_per_video = frames_per_video
		for label_name in ['real', 'fake']:
			label_dir = os.path.join(root, label_name)
			if not os.path.isdir(label_dir):
				continue
			for fname in os.listdir(label_dir):
				if fname.lower().endswith(('.mp4', '.mov', '.avi')):
					self.samples.append((os.path.join(label_dir, fname), 0 if label_name=='real' else 1))
	def __len__(self):
		return len(self.samples)
	def __getitem__(self, idx):
		path, label = self.samples[idx]
		cap = cv2.VideoCapture(path)
		frames = []
		if not cap.isOpened():
			return torch.zeros(3, 224, 224), label
		total = int(cap.get(cv2.CAP_PROP_FRAME_COUNT))
		idxs = np.linspace(0, max(0, total-1), self.frames_per_video).astype(int)
		for i in idxs:
			cap.set(cv2.CAP_PROP_POS_FRAMES, int(i))
			ok, frame = cap.read()
			if not ok:
				continue
			frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
			frame = cv2.resize(frame, (224, 224))
			frames.append(frame)
		cap.release()
		if not frames:
			frames = [np.zeros((224,224,3), dtype=np.uint8)]
		arr = np.stack(frames, axis=0).astype(np.float32) / 255.0
		arr = arr.mean(axis=0)  # naive temporal avg
		arr = (arr - 0.5) / 0.25
		arr = np.transpose(arr, (2, 0, 1))
		return torch.from_numpy(arr), label

class SmallImageCNN(nn.Module):
	def __init__(self):
		super().__init__()
		self.net = nn.Sequential(
			nn.Conv2d(3, 16, 3, padding=1), nn.ReLU(), nn.MaxPool2d(2),
			nn.Conv2d(16, 32, 3, padding=1), nn.ReLU(), nn.MaxPool2d(2),
			nn.AdaptiveAvgPool2d((1, 1))
		)
		self.fc = nn.Linear(32, 2)
	def forward(self, x):
		x = self.net(x)
		x = x.view(x.size(0), -1)
		return self.fc(x)


def train(data_root: str = './data', epochs: int = 1, batch_size: int = 2, lr: float = 1e-4, out: str = './video_model.pt'):
	device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
	dataset = VideoFrameDataset(os.path.join(data_root, 'train'))
	loader = DataLoader(dataset, batch_size=batch_size, shuffle=True, num_workers=2)
	model = SmallImageCNN().to(device)
	optim = torch.optim.AdamW(model.parameters(), lr=lr)
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
			if (i+1) % 5 == 0:
				print(f"epoch {epoch+1} step {i+1} loss {loss.item():.4f}")
	torch.save(model.state_dict(), out)
	print(f"saved to {out}")

if __name__ == '__main__':
	train()