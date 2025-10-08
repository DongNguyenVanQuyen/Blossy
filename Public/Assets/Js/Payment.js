document.addEventListener("DOMContentLoaded", function () {
  const confirmBtn = document.getElementById("confirm-payment");
  const selectedText = document.getElementById("payment-selected");

  confirmBtn.addEventListener("click", function () {
    const method = document.querySelector(
      "input[name='payment_method']:checked"
    );
    if (method) {
      selectedText.textContent =
        method.nextElementSibling.nextElementSibling.textContent;
      alert(`Bạn đã chọn phương thức thanh toán: ${selectedText.textContent}`);
    }
  });
});
