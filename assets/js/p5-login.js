let books = [];

function setup() {
  const canvas = createCanvas(windowWidth, windowHeight);
  canvas.parent('p5-login-canvas');
  colorMode(HSB, 360, 100, 100, 100);
  noStroke();

  for (let i = 0; i < 16; i++) {
    books.push({
      x: random(width),
      y: random(height),
      size: random(40, 70),
      speed: random(0.3, 1.2),
      rotation: random(TWO_PI),
      colorOffset: random(0, 255),
    });
  }
}

function draw() {
  clear();
  drawParticles();
  drawBooks();
}

function windowResized() {
  resizeCanvas(windowWidth, windowHeight);
}

function drawParticles() {
  for (let i = 0; i < 90; i++) {
    const x = (noise(i * 0.15, frameCount * 0.002) * width);
    const y = (noise(i * 0.33, frameCount * 0.002 + 1000) * height);
    fill(240, 165, 0, 14);
    circle(x, y, 8);
  }
}

function drawBooks() {
  books.forEach(book => {
    book.y -= book.speed;
    book.x += sin((frameCount + book.colorOffset) * 0.01) * 0.4;
    book.rotation += 0.002;

    if (book.y < -100) {
      book.y = height + random(40, 120);
      book.x = random(width);
    }

    push();
    translate(book.x, book.y);
    rotate(sin(book.rotation) * 0.12);
    const hue = (frameCount * 0.15 + book.colorOffset) % 360;
    fill(hue, 80, 90, 95);
    rect(-book.size * 0.35, -book.size * 0.5, book.size * 0.7, book.size, 10);
    fill(210, 24, 18, 90);
    rect(-book.size * 0.32, -book.size * 0.46, book.size * 0.64, book.size * 0.12, 5);
    pop();
  });
}
