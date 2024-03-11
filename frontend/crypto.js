require("dotenv").config();

const crypto = require("crypto");

const secretPassphrase = process.env.SECRET_PASS_PHRASE;

const iv = crypto.randomBytes(16);

// Derive a 256-bit (32-byte) key from the passphrase
const secretKey = crypto.createHash("sha256").update(secretPassphrase).digest();

// Encrypt function
function encrypt(id) {
  const cipher = crypto.createCipheriv("aes-256-cbc", secretKey, iv);
  let encrypted = cipher.update(id.toString(), "utf-8", "base64");
  encrypted += cipher.final("base64");
  return `${iv.toString("hex")}:${encrypted}`;
}

// Decrypt function
function decrypt(encryptedText) {
  const [ivHex, encrypted] = encryptedText.split(":");
  const decipher = crypto.createDecipheriv(
    "aes-256-cbc",
    secretKey,
    Buffer.from(ivHex, "hex")
  );
  let decrypted = decipher.update(encrypted, "base64", "utf-8");
  decrypted += decipher.final("utf-8");
  return parseInt(decrypted, 10);
}

const originalID = 8; // Replace with your ID

// Encrypt the ID
const encryptedID = encrypt(originalID);
console.log("Encrypted ID:", encryptedID);
console.log("Url:", "http://localhost:3000/register/visitor/" + encryptedID);

// Decrypt the ID
const decryptedID = decrypt(encryptedID);
console.log("Decrypted ID:", decryptedID);
