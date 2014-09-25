Полное описане тестового задания в файле back-end_developer_test_task.pdf 

Необходимо реализовать серверную часть сервиса для предоставления торговому терминалу текущих котировок и исторических данных для построения графиков.

Основные функции сервиса:
1. Получать текущие котировки по инструментам с сервера котировок;
2. Из полученных котировок обновлять и хранить историю для построения графиков
«японские свечи» для всех периодов;
3. Реализовывать возможность получения истории для построения графика
frontend­частью;
4. Реализовывать возможность получения текущих котировок для построения
таблицы и обновления последней точки на графике.

На данный момент реализованы пункты 1 и 2. Пишу 3й, т.е. json-апи для получения агрегированных данных

Для выполнения этой задачи я изучил фреймворк CodeIgniter - простой и удобный при работе с контроллерами и представлениями, но для нормального ORM и моделей нужно прикручивать что-то еще, например, Doctrine
