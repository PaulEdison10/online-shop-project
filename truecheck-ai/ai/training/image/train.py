import torch
import torch.nn as nn
from torch.utils.data import DataLoader, Dataset
from torchvision import transforms, models
from PIL import Image
import os

class ImageFolderDataset(Dataset):
	def __init__(self, root: str, transform=None):
		self.samples = []
		self.transform = transform
		for label_name in ['real', 'fake']:
			label_dir = os.path.join(root, label_name)
			if not os.path.isdir(label_dir):
				continue
			for fname in os.listdir(label_dir):
				if fname.lower().endswith(('.jpg', '.png', '.jpeg')):
					self.samples.append((os.path.join(label_dir, fname), 0 if label_name=='real' else 1))
	def __len__(self):
		return len(self.samples)
	def __getitem__(self, idx):
		path, label = self.samples[idx]
		img = Image.open(path).convert('RGB')
		if self.transform:
			img = self.transform(img)
		return img, label


def train(data_root: str = './data', epochs: int = 1, batch_size: int = 8, lr: float = 1e-4, out: str = './model.pt'):
	device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
	transform = transforms.Compose([
		transforms.Resize((224, 224)),
		transforms.ToTensor(),
		transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
	])
	dataset = ImageFolderDataset(os.path.join(data_root, 'train'), transform)
	loader = DataLoader(dataset, batch_size=batch_size, shuffle=True, num_workers=2)

	model = models.efficientnet_b0(weights=None)
	in_features = model.classifier[1].in_features
	model.classifier[1] = nn.Linear(in_features, 2)
	model.to(device)

	criterion = nn.CrossEntropyLoss()
	optim = torch.optim.AdamW(model.parameters(), lr=lr)

	model.train()
	for epoch in range(epochs):
		for i, (x, y) in enumerate(loader):
			x = x.to(device)
			y = y.to(device)
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