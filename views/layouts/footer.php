    </div>
    <script>

        async function fetchJson(url, data) {
            const formData = new FormData();
            for (const key in data) {
                formData.append(key, data[key]);
            }
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                return await response.json();
            } catch (e) {
                console.error(e);
                return { success: false, error: 'Network error' };
            }
        }
    </script>
</body>
</html>
