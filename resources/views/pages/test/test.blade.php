<form action="test"  method="post">
    @csrf

    <select id="cars  hidden" name="foo[]" multiple>
        <option value="volvo" selected>Volvo</option>
        <option value="saab">Saab</option>
        <option value="opel">Opel</option>
        <option value="volvo">Volvo</option>
        <option value="audi" selected>Audi</option>
    </select>
</br>
<input type="submit" name="submit">
</test>