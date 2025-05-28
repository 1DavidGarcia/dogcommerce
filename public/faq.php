<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>FAQ Smart Assistant</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet" />
  <style>
    :root {
      color-scheme: light dark;
    }
    body {
      font-family: 'Inter', sans-serif;
      background: #0f0f1f;
      height: 100vh;
      overflow: hidden;
    }
    @media (prefers-color-scheme: light) {
      body {
        background: #f4f4f4;
        color: #111;
      }
      .glass {
        background: rgba(255, 255, 255, 0.6);
        color: #111;
      }
      .input, .respuesta, .btn {
        background: #fff !important;
        color: #000 !important;
      }
    }
    #particles-js {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
      top: 0;
      left: 0;
    }
    .glass {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
      border-radius: 20px;
    }
    .fade-in {
      animation: fadeIn 1s ease-out forwards;
      opacity: 0;
      transform: translateY(30px);
    }
    @keyframes fadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="flex items-center justify-center p-6 text-white">
  <div id="particles-js"></div>

  <div class="glass p-8 w-full max-w-2xl fade-in">
    <h1 class="text-3xl font-bold mb-4 text-center">🤖 FAQ Smart Assistant</h1>
    <p class="text-white/80 text-center mb-6">Haz una pregunta como si hablaras con un experto.</p>

    <textarea id="pregunta" rows="4" class="input w-full p-3 bg-white/10 rounded-md border border-white/20 text-white placeholder-white/50" placeholder="Ej: ¿Cuántos módulos de RAM hay disponibles?"></textarea>

    <div class="flex gap-4 mt-4">
      <button onclick="consultar()" class="btn flex-1 bg-blue-600 hover:bg-blue-700 transition-all rounded-md py-2 font-semibold shadow">🔍 Consultar</button>
      <button onclick="verHistorial()" class="btn flex-1 bg-yellow-600 hover:bg-yellow-700 transition-all rounded-md py-2 font-semibold shadow">📜 Ver última</button>
      <button id="resetBtn" onclick="resetear()" class="btn flex-1 bg-gray-600 hover:bg-gray-700 transition-all rounded-md py-2 font-semibold shadow hidden">🔄 Otra pregunta</button>
      <button onclick="history.back()" class="btn flex-1 bg-red-600 hover:bg-red-700 transition-all rounded-md py-2 font-semibold shadow">🔙 Regresar</button>
    </div>


    <div id="respuesta" class="respuesta mt-6 p-4 bg-white/10 border border-white/10 rounded-md text-sm whitespace-pre-wrap min-h-[100px]">
      Esperando tu pregunta...
    </div>
  </div>

<script>
  const API_KEY = "AIzaSyCZ_y9DssYDt55Ly03Ioikz9jt_9lAt0tQ";
  const PROXY_URL = "https://api.allorigins.win/get?url=";
  const PASTEBIN_URL = "https://pastebin.com/raw/PX0sXtJQ";

  async function consultar() {
    const pregunta = document.getElementById("pregunta").value.trim().toLowerCase();
    const respuestaDiv = document.getElementById("respuesta");
    const resetBtn = document.getElementById("resetBtn");

    if (!pregunta) {
      respuestaDiv.innerText = "Por favor, escribe una pregunta.";
      return;
    }

    respuestaDiv.innerText = "Pensando... 🧠";

    try {
      const docResponse = await fetch(PROXY_URL + encodeURIComponent(PASTEBIN_URL));
      if (!docResponse.ok) throw new Error(`Error en Pastebin: ${docResponse.status}`);
      const docData = await docResponse.json();
      const docText = docData.contents;

      const regexInventario = /(\d+)\s(audifono|headphones|monitor|pantalla|mouse|ratón|teclado|keyboard|ram|memoria|modulo ram|router|wifi|impresora|printer|disco|almacenamiento|hdd|ssd|bocina|altavoces|parlantes|speakers)/gi;
      const inventario = {};
      let match;
      while ((match = regexInventario.exec(docText)) !== null) {
        inventario[match[2].toLowerCase()] = parseInt(match[1]);
      }

      const phoneMatches = docText.match(/\+?\d[\d\s\-()]{8,}/g);
      const phoneNumbers = phoneMatches ? phoneMatches.join("\n📞 ") : "No hay números de contacto disponibles.";

      if (pregunta.includes("andrade") || pregunta.includes("ingeniero") || pregunta.includes("puntos") || pregunta.includes("programación")) {
        const mensajeAndrade = docText.split("\n").find(l => l.toLowerCase().includes("andrade"));
        if (mensajeAndrade) {
          guardarHistorial(pregunta, mensajeAndrade);
          respuestaDiv.innerText = `📢 ${mensajeAndrade}\n\n📞 ${phoneNumbers}`;
          resetBtn.classList.remove("hidden");
          return;
        }
      }

      if (/inventario|lista de productos|qué tienen/.test(pregunta)) {
        let listaInventario = "📦 Inventario disponible:\n";
        for (let producto in inventario) {
          listaInventario += `- ${producto}: ${inventario[producto]}\n`;
        }
        listaInventario += `\n📞 ${phoneNumbers}`;
        guardarHistorial(pregunta, listaInventario);
        respuestaDiv.innerText = listaInventario;
        resetBtn.classList.remove("hidden");
        return;
      }

      for (let producto in inventario) {
        if (pregunta.includes(producto)) {
          let mensaje = `📦 Tenemos ${inventario[producto]} ${producto} disponibles.\n📞 ${phoneNumbers}`;
          guardarHistorial(pregunta, mensaje);
          respuestaDiv.innerText = mensaje;
          resetBtn.classList.remove("hidden");
          return;
        }
      }

      respuestaDiv.innerText = "Consultando IA...";
      const aiResponse = await fetch(
        `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${API_KEY}`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            contents: [{ parts: [{ text: pregunta }] }]
          })
        }
      );
      const aiData = await aiResponse.json();
      let respuestaAI = aiData.candidates?.[0]?.content?.parts?.[0]?.text || "No se obtuvo respuesta de Gemini.";
      respuestaAI += `\n\n📞 ${phoneNumbers}`;
      guardarHistorial(pregunta, respuestaAI);
      respuestaDiv.innerText = respuestaAI;
      resetBtn.classList.remove("hidden");

    } catch (error) {
      respuestaDiv.innerText = `❌ Ocurrió un error: ${error.message}\n\n📞 ${phoneNumbers}`;
      resetBtn.classList.remove("hidden");
    }
  }

  function resetear() {
    document.getElementById("pregunta").value = "";
    document.getElementById("respuesta").innerText = "Esperando tu pregunta...";
    document.getElementById("resetBtn").classList.add("hidden");
  }

  function guardarHistorial(pregunta, respuesta) {
    localStorage.setItem("faq_last_question", pregunta);
    localStorage.setItem("faq_last_answer", respuesta);
  }

  function verHistorial() {
    const last = localStorage.getItem("faq_last_answer");
    const pregunta = localStorage.getItem("faq_last_question");
    if (last && pregunta) {
      document.getElementById("respuesta").innerText = `📌 Última pregunta: ${pregunta}\n\n${last}`;
      document.getElementById("resetBtn").classList.remove("hidden");
    } else {
      alert("No hay historial guardado.");
    }
  }

  particlesJS("particles-js", {
    particles: {
      number: { value: 60, density: { enable: true, value_area: 800 } },
      color: { value: "#ffffff" },
      shape: { type: "circle" },
      opacity: { value: 0.2 },
      size: { value: 4, random: true },
      move: { enable: true, speed: 1.2 }
    },
    interactivity: {
      events: { onhover: { enable: true, mode: "repulse" } },
      modes: { repulse: { distance: 80, duration: 0.4 } }
    },
    retina_detect: true
  });
</script>

</body>
</html>