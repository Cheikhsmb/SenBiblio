let shelfBooks = [];

function setup() {
  const canvas = createCanvas(windowWidth, windowHeight);
  canvas.parent('p5-dashboard-canvas');
  colorMode(HSB, 360, 100, 100, 100);
  noStroke();

  for (let i = 0; i < 26; i++) {
    shelfBooks.push({
      x: random(width),
      y: random(height),
      size: random(40, 70),
      drift: random(0.2, 0.6),
      hue: random(20, 55),
      shelf: floor(random(3)),
      phase: random(TWO_PI),
    });
  }
}

function draw() {
  clear();
  drawLibraryGlow();
  drawShelves();
  drawShelfBooks();
}

function windowResized() {
  resizeCanvas(windowWidth, windowHeight);
}

function drawLibraryGlow() {
  for (let i = 0; i < 80; i++) {
    fill(240, 165, 0, 8);
    circle(width * noise(i * 0.13, frameCount * 0.001), height * noise(i * 0.21, frameCount * 0.001 + 100), 18);
  }
}

function drawShelves() {
  const shelves = 4;
  for (let i = 0; i < shelves; i++) {
    const y = height * 0.18 + i * 120;
    fill(255, 255, 255, 0.04);
    rect(0, y, width, 4);
    fill(255, 255, 255, 0.06);
    rect(0, y + 40, width, 2);
  }
}

function drawShelfBooks() {
  shelfBooks.forEach(book => {
    const baseY = height * 0.18 + book.shelf * 120 + 12;
    book.x = (book.x + sin(frameCount * 0.004 + book.phase) * book.drift) % width;
    if (book.x < -100) book.x = width + random(30, 140);
    const y = baseY + sin(frameCount * 0.008 + book.phase) * 8;

    push();
    translate(book.x, y);
    fill(book.hue, 78, 82, 96);
    rect(0, 0, book.size * 0.5, 60, 8);
    fill(0, 0, 100, 18);
    rect(6, 10, 8, 40, 4);
    pop();
  });
}
