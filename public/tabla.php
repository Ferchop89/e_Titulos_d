<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
   <head runat="server">
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <title>Como crear una tabla con divs</title>
      <style type="text/css">
         .Table
         {
            display: table;
         }
         .Title
         {
            display: table-caption;
            text-align: center;
            font-weight: bold;
            font-size: larger;
        }
        .Heading
        {
            display: table-row;
            font-weight: bold;
            text-align: center;
        }
        .Row
        {
            display: table-row;
        }
        .Cell
        {
            display: table-cell;
            border: solid;
            border-width: thin;
            padding-left: 5px;
            padding-right: 5px;
        }
    </style>
</head>
<body>
    <form id="form1" runat="server">
    <div class="Table">
        <div class="Title">
            <p>
                Esta es la tabla</p>
        </div>
        <div class="Heading">
            <div class="Cell">
                <p>
                    Primer Nombre</p>
            </div>
            <div class="Cell">
                <p>
                    Segundo Nombre</p>
            </div>
            <div class="Cell">
                <p>
                    Edad</p>
            </div>
        </div>
        <div class="Row">
            <div class="Cell">
                <p>
                    Juan</p>
            </div>
            <div class="Cell">
                <p>
                    Escutia</p>
            </div>
            <div class="Cell">
                <p>
                    28</p>
            </div>
        </div>
        <div class="Row">
            <div class="Cell">
                <p>
                    Pedro</p>
            </div>
            <div class="Cell">
                <p>
                    Infante</p>
            </div>
            <div class="Cell">
                <p>
                    27</p>
            </div>
        </div>
    </div>
    </form>
</body>
</html>
