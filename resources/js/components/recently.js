export default (() => {
    document.addEventListener("alpine:init", () => {
        Alpine.store("recently", {
            list: Alpine.$persist([]).as("recently"),
            toggleProduct(id) {
                // Если элемент уже есть — ничего не делаем
                if (this.list.includes(id)) return;

                // Если длина списка достигла 10 — удаляем первый (старый)
                if (this.list.length >= 10) {
                    this.list.shift();
                }

                // Добавляем новый в конец
                this.list.push(id);
            },
        });
    });
})();
