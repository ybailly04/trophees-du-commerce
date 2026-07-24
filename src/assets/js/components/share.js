import kjua from "kjua";

function initSocialShare() {
  document.querySelectorAll('[data-social-share]').forEach(root => {
    if (root._socialShareBound) return;
    root._socialShareBound = true;

    const link = root.getAttribute('data-social-share-link') || location.href;
    const title = root.getAttribute('data-social-share-title') || document.title;

    root.addEventListener('click', e => {
      const btn = e.target.closest('[data-social-share-type]');
      if (!btn) return;
      e.preventDefault();

      const type = btn.getAttribute('data-social-share-type');
      const u = encodeURIComponent(link);
      const t = encodeURIComponent(title);

      const map = {
        x: `https://twitter.com/intent/tweet?text=${t}&url=${u}`,
        linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${u}`,
        reddit: `https://www.reddit.com/submit?url=${u}&title=${t}`,
        telegram: `https://t.me/share/url?url=${u}&text=${t}`,
        whatsapp: `https://api.whatsapp.com/send?text=${t}%20${u}`,
        mail: `mailto:?subject=${t}&body=${t}%0A%0A${u}`,
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${u}`,
        pinterest: `https://www.pinterest.com/pin/create/button/?url=${u}&description=${t}`,
      };

      if (type === 'clipboard') {
        navigator.clipboard.writeText(link).then(() => {
          btn.setAttribute('data-social-share-success', '');
          setTimeout(() => btn.removeAttribute('data-social-share-success'), 2000);
        });
        return;
      }
      if(type === 'print'){
        print();
        return;
      }

      const url = map[type];
      if (url) window.open(url, '_blank', 'noopener,noreferrer');
    });
  });

  let QRcode = document.querySelector('[data-social-qr]');
  if(QRcode){
    // Generate an SVG QR via kjua
    const svg = kjua({
      text: window.location.href,
      render: "svg",
      crisp: true,
      minVersion: 1,
      ecLevel: "M",
      size: 540,
      fill: "#000000",
      back: "#FFFFFF",
      rounded: 0,
    });
    console.log(svg);
    // Let CSS control sizing
    svg.removeAttribute("width");
    svg.removeAttribute("height");
    svg.removeAttribute("style");

    // Insert into canvas (or multiple if needed)
    QRcode.querySelectorAll("[data-social-qr-canvas]").forEach((placeholder, i) => {
      const node = i === 0 ? svg : svg.cloneNode(true);
      placeholder.appendChild(node);
    });
  }
}

// Initialize Social Share
document.addEventListener('DOMContentLoaded', () => {
  initSocialShare();
});